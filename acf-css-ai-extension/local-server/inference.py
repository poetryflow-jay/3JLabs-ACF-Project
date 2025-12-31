import os
import torch
import logging
from flask import Flask, request, jsonify
from transformers import AutoTokenizer, AutoModelForCausalLM

# 로깅 설정
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)

# 모델 경로 설정
# 우선순위 1: J&Jenny AI Launcher (C:\my-ai)의 모델
EXTERNAL_MODEL_PATH = r"C:\my-ai\models\gemma-3-4b-it"
# 우선순위 2: 프로젝트 내장 모델 (models/gemma-3-4b-it)
INTERNAL_MODEL_PATH = os.path.abspath(os.path.join(os.path.dirname(__file__), '../../../models/gemma-3-4b-it'))

if os.path.exists(EXTERNAL_MODEL_PATH):
    MODEL_PATH = EXTERNAL_MODEL_PATH
    logger.info(f"Using external model from: {MODEL_PATH} (J&Jenny AI Launcher)")
else:
    MODEL_PATH = INTERNAL_MODEL_PATH
    logger.info(f"Using internal model from: {MODEL_PATH}")

logger.info(f"Final Model Path: {MODEL_PATH}")

try:
    # 토크나이저 및 모델 로드
    tokenizer = AutoTokenizer.from_pretrained(MODEL_PATH)
    model = AutoModelForCausalLM.from_pretrained(
        MODEL_PATH,
        torch_dtype=torch.bfloat16, # GPU 메모리 효율을 위해 bfloat16 사용
        device_map="auto" # 가능한 경우 GPU 자동 할당
    )
    logger.info("Model loaded successfully.")
except Exception as e:
    logger.error(f"Failed to load model: {e}")
    logger.info("Please ensure the model files exist in 'models/gemma-3-4b-it'.")
    # 모델 로드 실패 시에도 서버는 실행하되, 요청 시 에러 반환
    tokenizer = None
    model = None

@app.route('/generate', methods=['POST'])
def generate():
    if model is None:
        return jsonify({'error': 'Model not loaded. Check server logs.'}), 500

    data = request.json
    prompt = data.get('prompt', '')
    
    if not prompt:
        return jsonify({'error': 'No prompt provided'}), 400

    # 시스템 프롬프트 포맷팅 (Gemma Chat 템플릿 적용)
    # 실제로는 플러그인(PHP) 쪽에서 시스템 프롬프트를 포함해서 보낼 것이므로 여기서는 그대로 처리하거나
    # Gemma의 apply_chat_template을 사용하는 것이 좋음.
    # 여기서는 단순 텍스트 생성을 가정하고, PHP에서 포맷팅된 프롬프트를 받는다고 가정.
    
    try:
        inputs = tokenizer(prompt, return_tensors="pt").to(model.device)
        
        outputs = model.generate(
            **inputs,
            max_new_tokens=2048,
            do_sample=True,
            temperature=0.7,
            top_p=0.9,
        )
        
        # 입력 프롬프트 길이를 제외하고 생성된 텍스트만 디코딩
        input_length = inputs.input_ids.shape[1]
        response_text = tokenizer.decode(outputs[0][input_length:], skip_special_tokens=True)
        
        return jsonify({'response': response_text})
        
    except Exception as e:
        logger.error(f"Generation error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/status', methods=['GET'])
def status():
    return jsonify({
        'status': 'running',
        'model_loaded': model is not None,
        'model_path': MODEL_PATH,
        'device': str(model.device) if model else 'none'
    })

if __name__ == '__main__':
    # 외부 접속 허용하려면 host='0.0.0.0'
    app.run(host='127.0.0.1', port=5000)

