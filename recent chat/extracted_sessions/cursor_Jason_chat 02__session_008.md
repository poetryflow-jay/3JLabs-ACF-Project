---

**User**

고맙네, CTO 제이슨. 나의 권한으로 자네에게 순서 고정과 디테일 강화 등의 제안과 개발 작업 진행에 대해 전권을 위임하겠네. 이어지는 다음 작업도 잘 부탁하네. 자네만 믿네. - 3J Labs 사장, 창업자, CEO 이재인


---

**Cursor**

좋습니다, 대표님. 전권 위임 기준(순서 고정 + 디테일 강화 + 무오류)을 그대로 유지해서 **다음 라운드**는 이렇게 진행하겠습니다: 먼저 **스트리밍 UX를 “실사용 완성형(타이머/복구/복사/폭주 방지)”으로 더 다듬고**, 이어서 **AI 추천 팔레트 관리에 “불러오기/전체 내보내기”까지 추가**해 팀 운영 흐름을 완성하겠습니다.

```javascript
(function ($) {
  $(function () {
    const $provider = $('#jj-ai-provider');
    const $prompt = $('#jj-ai-prompt');
    const $gen = $('#jj-ai-generate');
    const $apply = $('#jj-ai-apply');
    const $cancel = $('#jj-ai-cancel');
    const $spinner = $('#jj-ai-spinner');
    const $result = $('#jj-ai-result');
    const $exp = $('#jj-ai-explanation');
    const $patch = $('#jj-ai-patch');
    const $streamArea = $('#jj-ai-stream-area');
    const $stream = $('#jj-ai-stream');
    const $streamStage = $('#jj-ai-stream-stage');
    const $streamElapsed = $('#jj-ai-stream-elapsed');
    const $streamSpeed = $('#jj-ai-stream-speed');
    const $streamEta = $('#jj-ai-stream-eta');
    const $streamOut = $('#jj-ai-stream-out');
    const $streamAutoScroll = $('#jj-ai-stream-autoscroll');
    const $streamCopy = $('#jj-ai-stream-copy');
    const $streamClear = $('#jj-ai-stream-clear');

    const $apiKeyRow = $('.jj-api-key-row');
    const $webgpuRow = $('.jj-webgpu-row');
    const $descOpenai = $('.desc-openai');
    const $descWebgpu = $('.desc-webgpu');
    const $descLocal = $('.desc-local');
    const $webgpuStatus = $('#jj-webgpu-status');
    const $webgpuLoad = $('#jj-webgpu-load');
    const $webgpuModel = $('#jj-webgpu-model');
    const $webgpuProgress = $('#jj-webgpu-progress');
    const $webgpuProgressFill = $('#jj-webgpu-progress .jj-progress-fill');
    const $webgpuProgressText = $('#jj-webgpu-progress .jj-progress-text');

    const $applyBrandPreset = $('#jj-ai-apply-brand-preset');
    
    // Settings form
    const $settingsForm = $('#jj-ai-settings-form');
    const $settingsMsg = $('#jj-ai-settings-msg');

    let lastPatchRaw = null;
    let lastPatch = null;
    let lastExplanation = '';
    let lastPrompt = '';

    // WebGPU state
    const webgpuState = {
      engine: null,
      loading: false,
      loadingPromise: null,
      loadingModelId: null,
      modelId: null,
      streaming: false,
      cancelled: false,
      abortController: null,
      metricTimer: null,
    };

    const streamStats = {
      startAt: 0,
      chars: 0,
      approxTokens: 0,
      lastMetricAt: 0,
      maxTokens: 1200,
    };

    function setLoading(on) {
      $spinner.css('visibility', on ? 'visible' : 'hidden');
      $gen.prop('disabled', on);
      if (on) $apply.prop('disabled', true);
    }

    function setStreamingUi(on) {
      if ($cancel.length) {
        $cancel.toggle(!!on);
        $cancel.prop('disabled', !on ? false : false);
      }
      if ($streamArea.length) {
        if (on) $streamArea.show();
      }
    }

    function setStreamStage(kind, text) {
      if (!$streamStage.length) return;
      $streamStage.attr('data-kind', kind || 'idle');
      $streamStage.text(text || '');
    }

    function formatTime(sec) {
      const s = Math.max(0, Math.floor(sec || 0));
      const m = Math.floor(s / 60);
      const r = s % 60;
      return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
    }

    function resetStreamStats() {
      streamStats.startAt = Date.now();
      streamStats.chars = 0;
      streamStats.approxTokens = 0;
      streamStats.lastMetricAt = 0;
      if ($streamElapsed.length) $streamElapsed.text('00:00');
      if ($streamSpeed.length) $streamSpeed.text('-');
      if ($streamEta.length) $streamEta.text('-');
      if ($streamOut.length) $streamOut.text('0');
    }

    function startMetricTimer() {
      stopMetricTimer();
      webgpuState.metricTimer = setInterval(function(){
        if (!webgpuState.streaming) return;
        updateStreamMetrics(true);
      }, 500);
    }

    function stopMetricTimer() {
      if (webgpuState.metricTimer) {
        clearInterval(webgpuState.metricTimer);
        webgpuState.metricTimer = null;
      }
    }

    function updateStreamMetrics(force) {
      const now = Date.now();
      if (!force && now - streamStats.lastMetricAt < 250) return;
      streamStats.lastMetricAt = now;

      const elapsedSec = streamStats.startAt ? ((now - streamStats.startAt) / 1000) : 0;
      const approxTokens = Math.max(0, Math.round(streamStats.chars / 4));
      streamStats.approxTokens = approxTokens;

      const tps = elapsedSec > 0 ? (approxTokens / elapsedSec) : 0;
      const remaining = Math.max(0, (streamStats.maxTokens || 0) - approxTokens);
      const etaSec = (tps > 0.1) ? (remaining / tps) : null;

      if ($streamElapsed.length) $streamElapsed.text(formatTime(elapsedSec));
      if ($streamSpeed.length) $streamSpeed.text(tps > 0.1 ? ('~' + tps.toFixed(1) + ' tok/s') : '-');
      if ($streamEta.length) $streamEta.text(etaSec === null ? '-' : ('~' + formatTime(etaSec)));
      if ($streamOut.length) {
        const k = streamStats.chars >= 1000 ? (streamStats.chars / 1000).toFixed(1) + 'k' : String(streamStats.chars);
        $streamOut.text(k + ' chars · ~' + approxTokens + ' tok');
      }
    }

    function setStreamText(text) {
      if (!$stream.length) return;
      $stream.text(text || '');
      streamStats.chars = (text || '').length;
      updateStreamMetrics(true);
    }

    function appendStreamText(chunk) {
      if (!$stream.length) return;
      const cur = $stream.text() || '';
      $stream.text(cur + (chunk || ''));
      streamStats.chars += (chunk || '').length;
      updateStreamMetrics(false);
      // 자동 스크롤 옵션
      const autoscroll = $streamAutoScroll.length ? $streamAutoScroll.is(':checked') : true;
      if (autoscroll) {
        try { $stream.scrollTop($stream[0].scrollHeight); } catch (e) {}
      }
    }

    function getSelectedProvider() {
      return ($provider.val() || 'openai').toString();
    }

    function syncProviderUi() {
      const p = getSelectedProvider();
      // Descriptions
      $descOpenai.hide();
      $descWebgpu.hide();
      $descLocal.hide();

      // Rows
      if (p === 'openai') {
        $apiKeyRow.show();
        $webgpuRow.hide();
        $descOpenai.show();
      } else if (p === 'webgpu') {
        $apiKeyRow.hide();
        $webgpuRow.show();
        $descWebgpu.show();
      } else if (p === 'local') {
        $apiKeyRow.hide();
        $webgpuRow.hide();
        $descLocal.show();
      } else {
        // Unknown provider: default to openai UI
        $apiKeyRow.show();
        $webgpuRow.hide();
        $descOpenai.show();
      }

      // WebGPU availability hint
      if (p === 'webgpu') {
        if (!isWebGpuAvailable()) {
          setWebGpuStatus('error', '이 브라우저에서는 WebGPU를 사용할 수 없습니다. (Chrome/Edge 최신 버전 권장)');
          $webgpuLoad.prop('disabled', true);
          $webgpuModel.prop('disabled', true);
        } else {
          $webgpuLoad.prop('disabled', false);
          // 모델 목록은 비동기로 채움
          populateWebgpuModelList();
          if (webgpuState.engine) {
            setWebGpuStatus('ok', '모델 로드됨: ' + (webgpuState.modelId || '알 수 없음'));
          } else {
            setWebGpuStatus('warn', '모델이 로드되지 않았습니다.');
          }
        }
      }
    }

    function setWebGpuStatus(kind, text) {
      if ($webgpuStatus.length === 0) return;
      let icon = 'info';
      let color = '#2271b1';
      if (kind === 'ok') { icon = 'yes-alt'; color = '#00a32a'; }
      if (kind === 'warn') { icon = 'warning'; color = '#f39c12'; }
      if (kind === 'error') { icon = 'dismiss'; color = '#d63638'; }

      $webgpuStatus.html(
        '<span class="dashicons dashicons-' + icon + '" style="color:' + color + ';"></span> ' +
        $('<div/>').text(text).html()
      );
    }

    function setWebGpuProgress(percent, text) {
      if ($webgpuProgress.length === 0) return;
      $webgpuProgress.show();
      const pct = Math.max(0, Math.min(100, Math.round(percent)));
      $webgpuProgressFill.css('width', pct + '%');
      if (text) {
        $webgpuProgressText.text(text + ' (' + pct + '%)');
      } else {
        $webgpuProgressText.text(pct + '%');
      }
    }

    function hideWebGpuProgress() {
      if ($webgpuProgress.length === 0) return;
      $webgpuProgress.hide();
    }

    function isWebGpuAvailable() {
      return typeof navigator !== 'undefined' && !!navigator.gpu;
    }

    async function waitForWebllm(timeoutMs) {
      const start = Date.now();
      while (Date.now() - start < timeoutMs) {
        if (window.webllm) return window.webllm;
        await new Promise((r) => setTimeout(r, 100));
      }
      return null;
    }

    function pickContextForAi(current) {
      const ctx = current || {};
      // 너무 큰 컨텍스트는 모델/브라우저에 부담이므로 핵심만 전달
      return {
        palettes: ctx.palettes || {},
        typography: ctx.typography || {},
        buttons: ctx.buttons || {},
        forms: ctx.forms || {},
      };
    }

    function buildSystemPrompt(context) {
      const contextJson = JSON.stringify(context || {});
      return [
        "너는 WordPress 'ACF CSS Manager' 플러그인의 스타일 전문가 AI야.",
        "사용자의 프롬프트(분위기, 브랜드, 업종 등)를 분석해서 JSON 포맷으로 스타일 설정(settings_patch)을 제안해야 해.",
        "",
        "[제약 사항]",
        "1. 반드시 유효한 JSON 객체만 출력할 것. (마크다운/설명/코드블록 금지)",
        "2. 최상위 키는 explanation(한국어 요약)과 settings_patch(설정 객체)만 허용.",
        "3. settings_patch는 아래 구조를 따름 (일부만 포함 가능):",
        "   - palettes: { brand: { primary_color, primary_color_hover, secondary_color, ... } }",
        "   - typography: { h1: { font_family, font_size, ... }, body: { ... } }",
        "   - buttons: { primary: { background_color, background_color_hover, border_color, border_color_hover, text_color, text_color_hover }, text: { text_color, text_color_hover } }",
        "   - forms: { field: { border_color_focus } }",
        "4. 색상은 반드시 Hex Code(#RRGGBB) 형태여야 함.",
        "5. 현재 설정 컨텍스트를 참고하여, 변경이 필요한 부분만 settings_patch에 포함할 것.",
        "",
        "[현재 설정 컨텍스트]",
        contextJson
      ].join("\n");
    }

    function extractJson(text) {
      if (!text) return '';
      const m = String(text).match(/```json\s*([\s\S]*?)\s*```/i);
      if (m && m[1]) return m[1].trim();
      const s = String(text);
      const start = s.indexOf('{');
      const end = s.lastIndexOf('}');
      if (start !== -1 && end !== -1 && end > start) return s.slice(start, end + 1);
      return s.trim();
    }

    function parseAiJsonResponse(rawText) {
      const jsonStr = extractJson(rawText);
      let parsed = null;
      try {
        parsed = JSON.parse(jsonStr);
      } catch (e) {
        throw new Error('AI 응답 JSON 파싱 실패: ' + e.message);
      }
      if (!parsed || typeof parsed !== 'object') {
        throw new Error('AI 응답이 올바른 JSON 객체가 아닙니다.');
      }
      if (!parsed.settings_patch || typeof parsed.settings_patch !== 'object') {
        throw new Error('AI 응답에 settings_patch가 없습니다.');
      }
      return {
        explanation: parsed.explanation || '',
        settings_patch: parsed.settings_patch
      };
    }

    function getModelList(webllm) {
      const list = webllm?.prebuiltAppConfig?.model_list || webllm?.prebuiltAppConfig?.modelList || [];
      return Array.isArray(list) ? list : [];
    }

    function getModelIdFromEntry(entry) {
      return (entry && (entry.model_id || entry.modelId)) ? String(entry.model_id || entry.modelId) : '';
    }

    function getSavedWebgpuModelId() {
      const saved = jjAiExt?.ai_settings?.webgpu_model_id;
      return saved ? String(saved) : '';
    }

    function pickRecommendedModelId(list) {
      if (!Array.isArray(list) || list.length === 0) return '';
      const gemma = list.find((m) => getModelIdFromEntry(m).toLowerCase().includes('gemma'));
      if (gemma) return getModelIdFromEntry(gemma);
      return getModelIdFromEntry(list[0]);
    }

    function getSelectedWebgpuModelId(webllm) {
      const list = getModelList(webllm);
      const saved = getSavedWebgpuModelId();

      const uiSelected = ($webgpuModel.length ? String($webgpuModel.val() || '') : '').trim();
      if (uiSelected) return uiSelected;

      if (saved && list.some((m) => getModelIdFromEntry(m) === saved)) {
        return saved;
      }

      const recommended = pickRecommendedModelId(list);
      if (recommended) return recommended;

      // Fallback (구버전/커스텀)
      return 'Llama-3.2-1B-Instruct-q4f16_1-MLC';
    }

    async function populateWebgpuModelList() {
      if ($webgpuModel.length === 0) return;
      if ($webgpuModel.data('jj-loaded')) return;

      // 초기 상태: 비활성
      $webgpuModel.prop('disabled', true);

      const webllm = await waitForWebllm(10000);
      if (!webllm) {
        $webgpuModel.html('<option value="">' + 'WebLLM 로드 실패' + '</option>').prop('disabled', true);
        return;
      }

      const list = getModelList(webllm);
      if (!list.length) {
        $webgpuModel.html('<option value="">' + '모델 목록을 찾을 수 없습니다' + '</option>').prop('disabled', true);
        return;
      }

      const saved = getSavedWebgpuModelId();
      const recommended = pickRecommendedModelId(list);
      const initial = saved && list.some((m) => getModelIdFromEntry(m) === saved) ? saved : (recommended || getModelIdFromEntry(list[0]));

      const opts = list.map((m) => {
        const id = getModelIdFromEntry(m);
        if (!id) return '';
        const lower = id.toLowerCase();
        const isGemma = lower.includes('gemma');

        // 메타가 있으면 가볍게 노출 (없으면 무시)
        const vram = (m.vram_required_MB || m.vram_required_mb || m.vramRequiredMB || m.vramRequiredMb);
        const vramText = vram ? (' · VRAM≈' + vram + 'MB') : '';
        const badge = isGemma ? ' (권장)' : '';

        return '<option value="' + $('<div/>').text(id).html() + '">' + $('<div/>').text(id + badge).html() + vramText + '</option>';
      }).filter(Boolean);

      $webgpuModel.html(opts.join(''));
      $webgpuModel.val(initial);
      $webgpuModel.prop('disabled', false);
      $webgpuModel.data('jj-loaded', true);
    }

    async function ensureWebGpuEngine() {
      if (!isWebGpuAvailable()) {
        throw new Error('WebGPU를 사용할 수 없습니다.');
      }

      const webllm = await waitForWebllm(10000);
      if (!webllm) {
        throw new Error('WebLLM 라이브러리를 불러오지 못했습니다. 네트워크/차단 여부를 확인해주세요.');
      }

      await populateWebgpuModelList();
      const modelId = getSelectedWebgpuModelId(webllm);

      // 이미 동일 모델이 로드되어 있으면 그대로 사용
      if (webgpuState.engine && webgpuState.modelId === modelId) {
        return webgpuState.engine;
      }

      // 동일 모델 로딩 중이면 해당 Promise 재사용
      if (webgpuState.loading && webgpuState.loadingPromise && webgpuState.loadingModelId === modelId) {
        return webgpuState.loadingPromise;
      }

      // 다른 모델이 로드되어 있었다면 교체 준비
      if (webgpuState.engine && webgpuState.modelId && webgpuState.modelId !== modelId) {
        try {
          if (typeof webgpuState.engine.dispose === 'function') {
            webgpuState.engine.dispose();
          } else if (typeof webgpuState.engine.unload === 'function') {
            webgpuState.engine.unload();
          }
        } catch (e) {
          // ignore
        }
        webgpuState.engine = null;
        webgpuState.loadingPromise = null;
        webgpuState.loadingModelId = null;
        webgpuState.modelId = null;
      }

      webgpuState.loading = true;
      webgpuState.loadingModelId = modelId;
      setWebGpuStatus('info', '모델 로드 중... (최초 1회 다운로드 필요)');
      setWebGpuProgress(1, '초기화');
      $webgpuLoad.prop('disabled', true);

      const progressCb = (report) => {
        // report: {progress: 0~1, text: string} 형태가 일반적
        const p = (report && typeof report.progress === 'number') ? report.progress : null;
        const txt = (report && report.text) ? report.text : '';
        if (p !== null) setWebGpuProgress(p * 100, txt || '다운로드/초기화');
      };

      const create = webllm.CreateMLCEngine || webllm.createMLCEngine;
      if (!create) {
        throw new Error('WebLLM API(CreateMLCEngine)를 찾을 수 없습니다. 라이브러리 버전을 확인해주세요.');
      }

      webgpuState.loadingPromise = create(modelId, {
        initProgressCallback: progressCb
      });

      try {
        webgpuState.engine = await webgpuState.loadingPromise;
        webgpuState.modelId = modelId;
        setWebGpuProgress(100, '완료');
        setWebGpuStatus('ok', '모델 로드 완료: ' + modelId);
        hideWebGpuProgress();
        return webgpuState.engine;
      } finally {
        webgpuState.loading = false;
        webgpuState.loadingModelId = null;
        $webgpuLoad.prop('disabled', false);
      }
    }

    function extractStreamDelta(chunk) {
      if (!chunk) return '';
      // OpenAI-like chunks
      const c0 = chunk?.choices?.[0];
      if (c0?.delta?.content) return String(c0.delta.content);
      if (c0?.message?.content) return String(c0.message.content);
      if (typeof chunk === 'string') return chunk;
      if (chunk?.text) return String(chunk.text);
      return '';
    }

    async function generateWithWebGpuStreaming(prompt) {
      const webllm = await waitForWebllm(10000);
      if (!webllm) {
        throw new Error('WebLLM을 불러오지 못했습니다.');
      }

      const engine = await ensureWebGpuEngine();
      const current = jjAiExt.current_settings || {};
      const context = pickContextForAi(current);

      const systemPrompt = buildSystemPrompt(context);
      const userPrompt = String(prompt);

      // WebLLM Chat Completions
      const chat = engine?.chat?.completions;
      if (!chat || typeof chat.create !== 'function') {
        throw new Error('WebLLM 엔진 API(chat.completions.create)를 찾을 수 없습니다.');
      }

      webgpuState.streaming = true;
      webgpuState.cancelled = false;
      try { webgpuState.abortController = new AbortController(); } catch (e) { webgpuState.abortController = null; }

      const baseArgs = {
        messages: [
          { role: 'system', content: systemPrompt },
          { role: 'user', content: userPrompt },
        ],
        temperature: 0.7,
        max_tokens: 1200,
      };

      let raw = '';
      let usedStreaming = false;

      // 1) streaming 시도 (가능하면 토큰 단위 출력)
      try {
        setStreamStage('running', '모델 출력 중');
        let streamResp = null;
        const tryArgs = $.extend(true, {}, baseArgs, { stream: true });
        if (webgpuState.abortController && webgpuState.abortController.signal) {
          tryArgs.signal = webgpuState.abortController.signal;
        }

        streamResp = chat.create(tryArgs);
        if (streamResp && typeof streamResp.then === 'function') {
          streamResp = await streamResp;
        }

        if (streamResp && typeof streamResp[Symbol.asyncIterator] === 'function') {
          usedStreaming = true;
          // header marker
          appendStreamText('');
          const startedAt = Date.now();
          let lastUiFlush = 0;
          let buffer = '';

          for await (const chunk of streamResp) {
            if (webgpuState.cancelled) break;
            const delta = extractStreamDelta(chunk);
            if (!delta) continue;

            raw += delta;
            buffer += delta;

            const now = Date.now();
            if (now - lastUiFlush > 60) {
              appendStreamText(buffer);
              buffer = '';
              lastUiFlush = now;
            }

            // 과도한 출력은 안전장치로 중단 (UI/메모리 보호)
            if (raw.length > 140000) {
              webgpuState.cancelled = true;
              appendStreamText('\n\n[중단] 출력이 너무 길어 중단합니다. 프롬프트를 더 짧고 명확하게(“JSON만 출력”) 요청해주세요.');
              throw new Error('출력이 너무 깁니다. JSON만 출력하도록 프롬프트를 단순화하세요.');
            }
          }

          if (buffer) appendStreamText(buffer);

          // 종료 표시(취소/완료)
          if (webgpuState.cancelled) {
            appendStreamText('\n\n[취소됨] 생성이 중지되었습니다.');
            throw new Error('cancelled');
          }

          const elapsed = Math.round((Date.now() - startedAt) / 1000);
          appendStreamText('\n\n[완료] 스트리밍 종료 (' + elapsed + 's)');
        }
      } catch (e) {
        // 스트리밍 실패 시 non-stream으로 폴백 (cancelled는 그대로 위에서 처리)
        if (String(e && e.message || e) === 'cancelled') throw e;
      }

      // 2) streaming 미지원이면 non-stream 폴백
      if (!usedStreaming) {
        setStreamStage('running', '모델 출력 중');
        const res = await chat.create(baseArgs);
        const content = res?.choices?.[0]?.message?.content || '';
        raw = String(content || '');
        setStreamText(raw);
      }

      setStreamStage('parsing', 'JSON 추출/파싱 중');
      return parseAiJsonResponse(raw);
    }

    function hasBrandPaletteInPatch(patch) {
      const b = patch?.palettes?.brand;
      if (!b || typeof b !== 'object') return false;
      return !!(b.primary_color || b.primary_color_hover || b.secondary_color || b.secondary_color_hover);
    }

    function shadeHex(hex, percent) {
      const h = String(hex || '').trim();
      if (!/^#?[0-9a-fA-F]{6}$/.test(h)) return hex;
      const clean = h.startsWith('#') ? h.slice(1) : h;
      const num = parseInt(clean, 16);
      let r = (num >> 16) & 255;
      let g = (num >> 8) & 255;
      let b = num & 255;
      const t = percent < 0 ? 0 : 255;
      const p = Math.abs(percent);
      r = Math.round((t - r) * p) + r;
      g = Math.round((t - g) * p) + g;
      b = Math.round((t - b) * p) + b;
      const out = (1 << 24) + (r << 16) + (g << 8) + b;
      return '#' + out.toString(16).slice(1).toUpperCase();
    }

    function ensurePath(obj, keys) {
      let cur = obj;
      keys.forEach((k) => {
        if (!cur[k] || typeof cur[k] !== 'object') cur[k] = {};
        cur = cur[k];
      });
      return cur;
    }

    /**
     * 코어의 “브랜드 팔레트 일괄 적용(버튼/폼/링크)”과 동일한 규칙
     * - primary / primary_hover 기반으로 버튼/링크/폼 포커스 색상 동기화
     */
    function applyBrandPalettePreset(rawPatch) {
      const current = jjAiExt.current_settings || {};
      const merged = $.extend(true, {}, current, rawPatch || {});

      const primary = merged?.palettes?.brand?.primary_color || '#0073e6';
      const primaryH = merged?.palettes?.brand?.primary_color_hover || shadeHex(primary, -0.12) || '#0051a3';

      const out = $.extend(true, {}, rawPatch || {});

      // buttons.primary.*
      ensurePath(out, ['buttons', 'primary']);
      out.buttons.primary.background_color = primary;
      out.buttons.primary.background_color_hover = primaryH;
      out.buttons.primary.border_color = primary;
      out.buttons.primary.border_color_hover = primaryH;

      // buttons.text.*
      ensurePath(out, ['buttons', 'text']);
      out.buttons.text.text_color = primary;
      out.buttons.text.text_color_hover = primaryH;

      // palettes.system.link_color
      ensurePath(out, ['palettes', 'system']);
      out.palettes.system.link_color = primary;

      // forms.field.border_color_focus
      ensurePath(out, ['forms', 'field']);
      out.forms.field.border_color_focus = primary;

      return out;
    }

    function computeEffectivePatch(rawPatch) {
      if (!rawPatch) return null;
      const enabled = ($applyBrandPreset.length ? $applyBrandPreset.is(':checked') : false);
      if (!enabled) return rawPatch;
      if (!hasBrandPaletteInPatch(rawPatch)) return rawPatch;
      return applyBrandPalettePreset(rawPatch);
    }

    function normalizeHex6(hex) {
      const h = String(hex || '').trim();
      if (!h) return '';
      const clean = h.startsWith('#') ? h : ('#' + h);
      if (/^#[0-9a-fA-F]{3}$/.test(clean)) {
        return ('#' + clean[1] + clean[1] + clean[2] + clean[2] + clean[3] + clean[3]).toUpperCase();
      }
      if (/^#[0-9a-fA-F]{6}$/.test(clean)) {
        return clean.toUpperCase();
      }
      return '';
    }

    function buildPalettePresetForSaving() {
      if (!lastPatch) return null;
      const current = jjAiExt.current_settings || {};
      const merged = $.extend(true, {}, current, lastPatch);

      const brand = merged?.palettes?.brand || {};
      const system = merged?.palettes?.system || {};

      const primary = normalizeHex6(brand.primary_color || '');
      const secondary = normalizeHex6(brand.secondary_color || '') || primary;

      if (!primary) return null;

      const primaryHover = normalizeHex6(brand.primary_color_hover || '') || shadeHex(primary, -0.12) || primary;
      const secondaryHover = normalizeHex6(brand.secondary_color_hover || '') || shadeHex(secondary, -0.12) || secondary;

      const siteBg = normalizeHex6(system.site_bg || '') || '#FFFFFF';
      const contentBg = normalizeHex6(system.content_bg || '') || '#FFFFFF';
      const textColor = normalizeHex6(system.text_color || '') || '#1D2327';
      const linkColor = normalizeHex6(system.link_color || '') || primary;

      return {
        brand: {
          primary: primary,
          secondary: secondary,
          primary_hover: primaryHover,
          secondary_hover: secondaryHover,
        },
        system: {
          site_bg: siteBg,
          content_bg: contentBg,
          text_color: textColor,
          link_color: linkColor,
        }
      };
    }

    function renderAiResult(settingsPatch, explanation, promptForCloud) {
      lastPatchRaw = settingsPatch || null;
      lastPatch = computeEffectivePatch(lastPatchRaw);
      lastExplanation = explanation || '';
      if (promptForCloud) lastPrompt = String(promptForCloud);

      const presetApplied = ($applyBrandPreset.length ? $applyBrandPreset.is(':checked') : false) && hasBrandPaletteInPatch(lastPatchRaw);

      // [Phase 7.3] Visual Diff
      const current = jjAiExt.current_settings || {};
      let diffHtml = '';

      if (window.jjJsonDiff && lastPatch) {
        try {
          // Simple merge for visualization
          const merged = $.extend(true, {}, current, lastPatch);
          const delta = window.jjJsonDiff.diff(current, merged);
          diffHtml = window.jjJsonDiff.formatHtml(delta);
        } catch (e) {
          console.error('Diff error:', e);
          diffHtml = '<pre>' + JSON.stringify(lastPatch, null, 2) + '</pre>';
        }
      } else {
        diffHtml = '<pre>' + JSON.stringify(lastPatch, null, 2) + '</pre>';
      }

      const expText = presetApplied
        ? (lastExplanation ? (lastExplanation + '\n\n[프리셋] 브랜드 팔레트 기준 자동 매핑이 포함되었습니다.') : '[프리셋] 브랜드 팔레트 기준 자동 매핑이 포함되었습니다.')
        : lastExplanation;
      $exp.text(expText);
      $patch.html(diffHtml);

      // Add Cloud Export Button if not exists
      if ($('#jj-ai-cloud-export').length === 0) {
        const $cloudBtn = $('<button type="button" class="button button-secondary" id="jj-ai-cloud-export" style="margin-left:10px;">☁️ Cloud 저장</button>');
        $apply.after($cloudBtn);

        // [Phase 7.4] Cloud Export Handler
        $cloudBtn.on('click', function() {
          if (!lastPatch) return;
          if (!confirm('이 스타일을 클라우드(내 템플릿)에 저장하시겠습니까?')) return;

          const btn = $(this);
          btn.prop('disabled', true).text('저장 중...');

          $.ajax({
            url: jjAiExt.ajax_url,
            type: 'POST',
            data: {
              action: 'jj_ai_export_cloud',
              nonce: jjAiExt.nonce,
              settings_patch: lastPatch,
              prompt: lastPrompt || ''
            },
            success: function(res) {
              if (res.success) {
                alert('클라우드에 저장되었습니다! 공유 코드: ' + res.data.share_code);
              } else {
                alert(res.data.message || '저장 실패');
              }
            },
            error: function(xhr, status, error) {
              alert('오류: ' + error);
            },
            complete: function() {
              btn.prop('disabled', false).text('☁️ Cloud 저장');
            }
          });
        });
      }

      // Save as "Recommended Palette" preset (Core presets.js에서 카드로 노출)
      if ($('#jj-ai-save-palette-preset').length === 0) {
        const $saveBtn = $('<button type="button" class="button" id="jj-ai-save-palette-preset" style="margin-left:10px;">⭐ 추천 팔레트로 저장</button>');
        const $anchor = $('#jj-ai-cloud-export').length ? $('#jj-ai-cloud-export') : $apply;
        $anchor.after($saveBtn);

        $saveBtn.on('click', function() {
          const preset = buildPalettePresetForSaving();
          if (!preset) {
            alert('저장할 팔레트 데이터가 없습니다. (브랜드 Primary 색상이 필요합니다)');
            return;
          }

          const base = (lastPrompt || 'AI Palette').replace(/\s+/g, ' ').trim();
          const short = base.length > 36 ? (base.slice(0, 36) + '…') : base;
          const defaultName = 'AI · ' + short;
          const name = window.prompt('추천 팔레트 이름을 입력하세요:', defaultName);
          if (!name) return;

          const btn = $(this);
          btn.prop('disabled', true).text('저장 중...');

          $.ajax({
            url: jjAiExt.ajax_url,
            type: 'POST',
            data: {
              action: 'jj_ai_save_palette_preset',
              nonce: jjAiExt.nonce,
              name: name,
              note: lastPrompt || '',
              preset_json: JSON.stringify(preset),
            },
            success: function(res) {
              if (res && res.success) {
                alert((res.data && res.data.message ? res.data.message : '저장되었습니다.') + '\n\n스타일 센터 > 팔레트 시스템 > “빠른 시작: 추천 팔레트”에서 확인할 수 있습니다.');
              } else {
                alert(res?.data?.message || '저장 실패');
              }
            },
            error: function(_, __, err) {
              alert('AJAX 오류: ' + err);
            },
            complete: function() {
              btn.prop('disabled', false).text('⭐ 추천 팔레트로 저장');
            }
          });
        });
      }

      $result.show();
      $apply.prop('disabled', !lastPatch);
    }

    // Save Settings
    $settingsForm.on('submit', function(e) {
      e.preventDefault();
      const data = $(this).serialize();
      
      $settingsMsg.text('저장 중...').css('color', '#666');
      
      $.ajax({
        url: jjAiExt.ajax_url,
        type: 'POST',
        data: data + '&action=jj_ai_save_settings&nonce=' + jjAiExt.nonce,
        success: function (res) {
          if (!res || !res.success) {
            $settingsMsg.text(res?.data?.message || '저장 실패').css('color', '#d63638');
            return;
          }
          $settingsMsg.text(res.data.message || '저장 완료').css('color', '#00a32a');
          setTimeout(() => $settingsMsg.text(''), 3000);
        },
        error: function (_, __, err) {
          $settingsMsg.text('AJAX 오류: ' + err).css('color', '#d63638');
        }
      });
    });

    $provider.on('change', function() {
      syncProviderUi();
    });
    syncProviderUi();

    $webgpuModel.on('change', function() {
      const selected = String($(this).val() || '').trim();
      if (!selected) return;

      // 이미 로드된 모델과 다르면 재로딩 필요
      if (webgpuState.engine && webgpuState.modelId && selected !== webgpuState.modelId) {
        const ok = confirm(
          '이미 로드된 모델이 있습니다:\n' +
          '- 현재: ' + webgpuState.modelId + '\n' +
          '- 선택: ' + selected + '\n\n' +
          '모델을 변경하면 다시 로드가 필요합니다. 계속할까요?'
        );
        if (!ok) {
          $(this).val(webgpuState.modelId);
          return;
        }

        try {
          if (typeof webgpuState.engine.dispose === 'function') {
            webgpuState.engine.dispose();
          } else if (typeof webgpuState.engine.unload === 'function') {
            webgpuState.engine.unload();
          }
        } catch (e) {
          // ignore
        }
        webgpuState.engine = null;
        webgpuState.loadingPromise = null;
        webgpuState.loadingModelId = null;
        webgpuState.modelId = null;

        setWebGpuStatus('warn', '모델이 변경되었습니다. “모델 로드”를 다시 실행하세요.');
      }
    });

    $applyBrandPreset.on('change', function() {
      if (!lastPatchRaw) return;
      // 프리셋 on/off에 따라 Diff/적용 대상 패치 재계산
      renderAiResult(lastPatchRaw, lastExplanation, lastPrompt);
    });

    $webgpuLoad.on('click', async function() {
      if (!isWebGpuAvailable()) {
        alert('이 브라우저는 WebGPU를 지원하지 않습니다. Chrome/Edge 최신 버전을 사용해주세요.');
        return;
      }
      if (!confirm('최초 1회 모델 다운로드가 필요할 수 있습니다(약 2GB). 지금 로드할까요?')) {
        return;
      }

      try {
        await ensureWebGpuEngine();
        syncProviderUi();
      } catch (e) {
        console.error(e);
        setWebGpuStatus('error', e.message || String(e));
        alert(e.message || '모델 로드 실패');
      }
    });

    $cancel.on('click', function() {
      if (!webgpuState.streaming) return;
      if (!confirm('생성을 중지할까요?')) return;

      webgpuState.cancelled = true;
      setStreamStage('cancelled', '취소 요청됨');
      try {
        if (webgpuState.abortController) {
          webgpuState.abortController.abort();
        }
      } catch (e) {}

      try {
        if (webgpuState.engine) {
          if (typeof webgpuState.engine.interruptGenerate === 'function') {
            webgpuState.engine.interruptGenerate();
          } else if (typeof webgpuState.engine.abortGenerate === 'function') {
            webgpuState.engine.abortGenerate();
          } else if (typeof webgpuState.engine.interrupt === 'function') {
            webgpuState.engine.interrupt();
          }
        }
      } catch (e) {}

      appendStreamText('\n\n[취소 요청됨] 가능한 한 빨리 중지합니다...');
    });

    $streamCopy.on('click', async function() {
      const text = ($stream.length ? ($stream.text() || '') : '');
      if (!text) {
        alert('복사할 출력이 없습니다.');
        return;
      }

      try {
        if (navigator && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
          await navigator.clipboard.writeText(text);
          alert('스트리밍 출력이 클립보드에 복사되었습니다.');
          return;
        }
      } catch (e) {
        // fallback below
      }

      // Fallback: prompt
      window.prompt('아래 내용을 복사하세요:', text.slice(0, 5000));
    });

    $streamClear.on('click', function() {
      if (!$stream.length) return;
      if (!$stream.text()) return;
      if (!confirm('스트리밍 출력(진행 로그)을 지울까요?')) return;
      setStreamText('');
      setStreamStage('idle', '대기');
    });

    $gen.on('click', async function () {
      const prompt = ($prompt.val() || '').trim();
      if (!prompt) {
        alert('프롬프트를 입력하세요.');
        return;
      }

      setLoading(true);
      lastPrompt = prompt;

      const provider = getSelectedProvider();
      if (provider === 'webgpu') {
        try {
          setStreamingUi(true);
          setStreamText('');
          resetStreamStats();
          setStreamStage('running', '모델 출력 중');
          startMetricTimer();

          if (!webgpuState.engine) {
            const ok = confirm('WebGPU 모델이 아직 로드되지 않았습니다. 지금 로드할까요? (최초 1회 다운로드 필요)');
            if (!ok) {
              return;
            }
            await ensureWebGpuEngine();
          }

          const out = await generateWithWebGpuStreaming(prompt);
          setStreamStage('parsing', 'JSON 파싱/검증 중');
          renderAiResult(out.settings_patch, out.explanation, prompt);
          setStreamStage('done', '완료');
        } catch (e) {
          console.error(e);
          if (String(e && e.message || e) === 'cancelled') {
            setStreamStage('cancelled', '취소됨');
            alert('생성이 취소되었습니다.');
          } else {
            setStreamStage('error', '오류');
            appendStreamText('\n\n[오류] ' + ((e && e.message) ? e.message : String(e)));
            alert((e && e.message) ? e.message : 'WebGPU 생성 실패');
          }
        } finally {
          webgpuState.streaming = false;
          webgpuState.cancelled = false;
          webgpuState.abortController = null;
          stopMetricTimer();
          setStreamingUi(false);
          setLoading(false);
        }
        return;
      }

      $.ajax({
        url: jjAiExt.ajax_url,
        type: 'POST',
        data: {
          action: 'jj_ai_generate_style',
          nonce: jjAiExt.nonce,
          prompt,
        },
        success: function (res) {
          if (!res || !res.success) {
            alert(res?.data?.message || '생성 실패');
            return;
          }
          renderAiResult(res.data.settings_patch || null, res.data.explanation || '', prompt);
        },
        error: function (_, __, err) {
          alert('AJAX 오류: ' + err);
        },
        complete: function () {
          setLoading(false);
        },
      });
    });

    $apply.on('click', function () {
      if (!lastPatch) return;
      if (!confirm('현재 설정에 AI 제안을 적용(저장)합니다. 계속할까요?')) return;

      setLoading(true);

      $.ajax({
        url: jjAiExt.ajax_url,
        type: 'POST',
        data: {
          action: 'jj_ai_apply_style',
          nonce: jjAiExt.nonce,
          settings_patch: lastPatch,
        },
        success: function (res) {
          if (!res || !res.success) {
            alert(res?.data?.message || '적용 실패');
            return;
          }
          alert(res.data.message || '적용 완료');
          $apply.prop('disabled', true);
          location.reload(); // Refresh to see changes
        },
        error: function (_, __, err) {
          alert('AJAX 오류: ' + err);
        },
        complete: function () {
          setLoading(false);
        },
      });
    });
  });
})(jQuery);

```

```php
<?php
/**
 * Plugin Name:       ACF CSS - Advanced Custom Fonts&Colors&Styles Setting Manager (Master)
 * Plugin URI:        https://j-j-labs.com
 * Description:       WordPress 웹사이트의 모든 스타일 요소(색상 팔레트, 타이포그래피, 버튼, 폼)를 중앙에서 일관되게 관리하는 통합 스타일 관리 플러그인입니다. Free 버전은 기본적인 스타일 관리 기능을 제공하며, 브랜드 일관성을 유지하고 디자인 시스템을 효율적으로 운영할 수 있습니다. Pro 버전 플러그인을 함께 설치하면 Basic, Premium, Unlimited 기능을 사용할 수 있습니다. WordPress Customizer와 완벽 통합되어 실시간 미리보기와 함께 직관적인 스타일 관리가 가능합니다.
 * Version:           8.0.0
 * Author:            3J Labs
 * Created by:        Jay & Jason & Jenny
 * Author URI:        https://poetryflow.blog
 * Text Domain:       acf-css-really-simple-style-management-center
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * ⚠️ 중요: Git 경로와 로컬 경로는 완전히 별개입니다
 * 
 * - 로컬 작업 경로: C:\Users\computer\OneDrive\Desktop\클라우드 백업\OneDrive\문서\jj-css-premium
 * - Git worktree 경로: C:\Users\computer\.cursor\worktrees\jj-css-premium\tci
 * - 두 경로는 서로 독립적이며, Git에서 작업한 내용은 반드시 로컬 경로에 동기화해야 함
 * - 자세한 내용은 DEVELOPMENT_PRINCIPLES.md의 "Git 및 로컬 경로 관리 원칙" 섹션 참조
 */

// 플러그인 상수 정의
// [v5.1.6] 전면 재검토 및 오류 방지: 안전한 파일 로더 추가, 모든 버전의 require_once 안전 처리, 결제 유도 문구 추가, 플러그인 목록 페이지 바로가기 링크 추가
// [v5.1.6] Comprehensive review and error prevention: Safe file loader added, all versions' require_once safely handled, purchase prompts added, plugin list page quick links added
// [v1.0.2] 모든 버전 플러그인 활성화 안전성 최종 확보, WordPress 함수 호출 안전 처리
define( 'JJ_STYLE_GUIDE_VERSION', '8.0.0' ); // [v7.0.0] Operation Deep Dive - Stability & Reliability Overhaul

// WordPress 함수가 로드되었는지 확인 후 상수 정의
if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
    if ( function_exists( 'plugin_dir_path' ) ) {
        define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 직접 경로 계산
        define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
    }
}

if ( ! defined( 'JJ_STYLE_GUIDE_URL' ) ) {
    if ( function_exists( 'plugin_dir_url' ) ) {
        define( 'JJ_STYLE_GUIDE_URL', plugin_dir_url( __FILE__ ) );
    } else {
        // WordPress가 로드되기 전에는 기본값 사용 (나중에 업데이트됨)
        define( 'JJ_STYLE_GUIDE_URL', '' );
    }
}
define( 'JJ_STYLE_GUIDE_OPTIONS_KEY', 'jj_style_guide_options' );
define( 'JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY', 'jj_style_guide_temp_options' );
define( 'JJ_STYLE_GUIDE_ADMIN_TEXT_KEY', 'jj_style_guide_admin_texts' );
define( 'JJ_STYLE_GUIDE_PAGE_SLUG', 'acf-css-really-simple-style-guide' );
define( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY', 'jj_style_guide_cockpit_page_id' );
define( 'JJ_STYLE_GUIDE_DEBUG', defined( 'WP_DEBUG' ) && WP_DEBUG );
// Pro 버전 활성화 코드 확인 (plugins_loaded 훅에서 처리)
// WordPress가 로드되기 전에는 기본값 사용
define( 'JJ_STYLE_GUIDE_LICENSE_TYPE', 'MASTER' ); // 기본값, plugins_loaded에서 업데이트됨
define( 'JJ_STYLE_GUIDE_EDITION', 'master' ); // 기본 에디션은 free

// [v3.8.0] 환경 구분
if ( ! defined( 'JJ_STYLE_GUIDE_ENV' ) ) {
    define( 'JJ_STYLE_GUIDE_ENV', 'live' );
}

// [v5.3.7] Free 버전은 독립적으로 작동합니다. Master 버전 의존성 제거
// Free 버전은 자체 includes 폴더에 필요한 모든 파일을 포함합니다.

// [v5.4.1] 안전한 파일 로더 적용
$safe_loader = null;
try {
    $safe_loader_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-loader.php';
    if ( file_exists( $safe_loader_path ) ) {
        require_once $safe_loader_path;
        if ( class_exists( 'JJ_Safe_Loader' ) && method_exists( 'JJ_Safe_Loader', 'instance' ) ) {
            $safe_loader = JJ_Safe_Loader::instance();
        }
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Failed: ' . $e->getMessage() );
} catch ( Error $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Safe Loader Load Fatal Error: ' . $e->getMessage() );
}

// 라이센스 매니저 로드
if ( $safe_loader ) {
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php', false );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-version-features.php', true );
    $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-code-integrity-monitor.php', true );
}

// 핵심 로직 파일 로드 (Master 전용 파일은 아직 비활성화)
try {
    if ( $safe_loader && method_exists( $safe_loader, 'safe_require' ) ) {
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-handler.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-error-logger.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-admin-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-backup-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/visual-command-center/class-jj-visual-command-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/ai-style-intelligence/class-jj-smart-palette.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-cloud-manager.php', true );
        // [Phase 3] 템플릿 마켓
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/cloud-manager/class-jj-template-market.php', true );
        // [Phase 5] Partner Hub
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/modules/partner-hub/class-jj-partner-hub.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-labs-center.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-selector-registry.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-injector.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-options-cache.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-edition-controller.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-theme-metadata.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-0-customizer.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-1-css-vars.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-2-php-filters.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-strategy-3-dequeue.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-sync.php', false );
        // [Phase 5.2] REST API (설정 조회/업데이트)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/api/class-jj-style-guide-rest-api.php', true );
        // [Phase 5.3] 확장 플러그인 시스템 (Extension API)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/interface-jj-style-guide-extension.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/extensions/class-jj-extension-manager.php', true );
        // [Phase 5.2] Webhooks (자동화)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/integrations/class-jj-webhook-manager.php', true );
        // [Phase 5 B] Multisite Network Control
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/multisite/class-jj-multisite-controller.php', true );
        
        // Master 전용 파일들 (일단 주석 처리)
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-sync-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php', false );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-memory-manager.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-safe-mode.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-css-optimizer.php', true );
        $safe_loader::safe_require( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-issuer.php', false );
    } else {
        // Fallback for Safe Loader missing
        require_once JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
        // ... (Fallback은 일단 생략, Safe Loader가 있다고 가정)
    }
} catch ( Exception $e ) {
    if ( function_exists( 'error_log' ) ) error_log( 'Master Load Error: ' . $e->getMessage() );
}



/**
 * 활성화 훅 래퍼 함수 (치명적 오류 방지)
 * [v5.1.7] 전체 함수를 try-catch로 감싸고 상수 정의 확인 추가
 */
function jj_simple_style_guide_master_activate() {
    try {
        // 상수 정의 확인
        if ( ! defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // 상수가 정의되지 않은 경우 기본 경로 사용
            if ( function_exists( 'plugin_dir_path' ) ) {
                define( 'JJ_STYLE_GUIDE_PATH', plugin_dir_path( __FILE__ ) );
            } else {
                define( 'JJ_STYLE_GUIDE_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
            }
        }
        
        // 활성화 매니저 클래스가 로드되지 않은 경우 로드 시도
        
        if ( ! class_exists( 'JJ_Activation_Manager' ) ) {
            $activation_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-activation-manager.php';
            if ( file_exists( $activation_file ) ) {
                require_once $activation_file;
            } else {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 파일을 찾을 수 없습니다. 기본 활성화만 수행합니다.' );
                }
                return;
            }
        }
        
        // 클래스가 존재하는지 확인 후 활성화 실행
        if ( class_exists( 'JJ_Activation_Manager' ) && method_exists( 'JJ_Activation_Manager', 'activate' ) ) {
            try {
                JJ_Activation_Manager::activate();
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 오류 발생 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Really Simple Style Management Center: 활성화 중 치명적 오류 발생 - ' . $e->getMessage() );
                }
            }
        } else {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: 활성화 매니저 클래스를 사용할 수 없습니다.' );
            }
        }
    } catch ( Exception $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 예외 발생 - ' . $e->getMessage() );
        }
    } catch ( Error $e ) {
        if ( function_exists( 'error_log' ) ) {
            error_log( 'ACF CSS Really Simple Style Management Center: 활성화 훅 실행 중 치명적 오류 발생 - ' . $e->getMessage() );
        }
    }
}
register_activation_hook( __FILE__, 'jj_simple_style_guide_master_activate' );

/**
 * Free 버전 전용 메인 컨트롤 클래스
 */
final class JJ_Simple_Style_Guide_Master {

    private static $instance = null;
    private $options = array();
    private $cockpit_page_id = 0;
    private $options_key = '';
    private $brand_colors = array();
    private $environment_map = array();
    private $adapters_config = array();
    private $license_manager = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // 상수 정의 확인 후 할당
        if ( defined( 'JJ_STYLE_GUIDE_OPTIONS_KEY' ) ) {
            $this->options_key = JJ_STYLE_GUIDE_OPTIONS_KEY;
        }
        
        if ( class_exists( 'JJ_License_Manager' ) ) {
            $this->license_manager = JJ_License_Manager::instance();
        }
        
        if ( defined( 'JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY' ) && function_exists( 'get_option' ) ) {
            $this->cockpit_page_id = (int) get_option( JJ_STYLE_GUIDE_COCKPIT_PAGE_ID_KEY );
        }

        if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
            // [v5.3.7] Free 버전은 자체 config 폴더에서 로드
            $config_file = JJ_STYLE_GUIDE_PATH . 'config/adapters-config.php';
            if ( file_exists( $config_file ) ) {
                $this->adapters_config = include( $config_file );
                // [v5.5.0] Edition Controller를 통한 동적 어댑터 필터링
                if ( class_exists( 'JJ_Edition_Controller' ) ) {
                    $this->adapters_config = JJ_Edition_Controller::instance()->filter_adapters( $this->adapters_config );
                }
                
                
            }
        }

        // WordPress 함수가 로드된 후에만 훅 등록
        // [v5.1.9] plugin_basename() 안전 호출 추가
        // [v5.3.9] 모든 훅 등록을 조건문 안으로 이동하여 안전성 확보
        if ( function_exists( 'add_action' ) && function_exists( 'add_filter' ) && function_exists( 'plugin_basename' ) ) {
            add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ) );
            add_action( 'admin_menu', array( $this, 'add_admin_menu_links' ) );
            $plugin_basename = function_exists( 'plugin_basename' ) ? plugin_basename( __FILE__ ) : basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_plugin_settings_link' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
            add_action( 'wp_ajax_jj_save_style_guide', array( $this, 'save_settings_ajax' ) );
            add_action( 'wp_ajax_jj_apply_temp_palette', array( $this, 'apply_temp_palette_ajax' ) );
            add_action( 'wp_ajax_jj_save_temp_palette', array( $this, 'save_temp_palette_ajax' ) );
            // [v3.8.0] Customizer 색상 새로고침 (팔레트 동기화)
            if ( class_exists( 'JJ_Customizer_Sync' ) ) {
                add_action( 'wp_ajax_jj_load_current_colors', array( 'JJ_Customizer_Sync', 'ajax_load_current_colors' ) );
            }
            // [v3.8.0] 섹션별 내보내기/불러오기 (팔레트 등)
            add_action( 'wp_ajax_jj_export_section', array( $this, 'ajax_export_section' ) );
            add_action( 'wp_ajax_jj_import_section', array( $this, 'ajax_import_section' ) );
            // [AI Presets] AI Extension에서 저장한 추천 팔레트 관리(핀/삭제 등)
            add_action( 'wp_ajax_jj_ai_palette_preset_manage', array( $this, 'ajax_ai_palette_preset_manage' ) );
            add_action( 'init', array( $this, 'load_options_and_init_strategies' ), 5 );
            add_action( 'init', array( $this, 'init_sync_manager' ), 10 );
            
            
            if ( JJ_STYLE_GUIDE_DEBUG ) {
                add_action( 'wp_footer', array( $this, 'render_debug_info' ), 999 );
                add_action( 'admin_footer', array( $this, 'render_debug_info' ), 999 );
            }
        }

        // Customizer 통합 (제한적)
        
        try {
            $customizer_manager_path = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-customizer-manager.php';
            if ( file_exists( $customizer_manager_path ) ) {
                require_once $customizer_manager_path;
                if ( class_exists( 'JJ_Customizer_Manager' ) ) {
                    $customizer_manager = JJ_Customizer_Manager::instance();
                    if ( method_exists( $customizer_manager, 'init' ) ) {
                        $customizer_manager->init();
                    }
                }
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Really Simple Style Management Center: Customizer Manager 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }

    }

    /**
     * Free 버전: 어댑터 제한
     * 
     * @param array $config
     * @return array
     */
    private function limit_adapters_for_free( $config ) {
        // Free 버전: 테마 2개만 지원 (Twenty Twenty-Four, Twenty Twenty-Five)
        $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
        if ( isset( $config['themes'] ) && is_array( $config['themes'] ) ) {
            $config['themes'] = array_intersect_key( $config['themes'], array_flip( $allowed_themes ) );
        }

        // Free 버전: 플러그인 1개만 지원 (WooCommerce)
        $allowed_plugins = array( 'woocommerce' );
        if ( isset( $config['spokes'] ) && is_array( $config['spokes'] ) ) {
            $config['spokes'] = array_intersect_key( $config['spokes'], array_flip( $allowed_plugins ) );
        }

        return $config;
    }

    /**
     * Free 버전 워터마크 추가
     */
    public function add_free_watermark() {
        if ( ! is_admin() && current_user_can( 'manage_options' ) ) {
            echo '<div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: #fff; padding: 8px 12px; font-size: 11px; border-radius: 4px; z-index: 99999; font-family: Arial, sans-serif;">';
            echo '<a href="' . esc_url( admin_url( 'options-general.php?page=' . JJ_STYLE_GUIDE_PAGE_SLUG ) ) . '" style="color: #fff; text-decoration: none;">JJ Style Guide Free</a>';
            echo '</div>';
        }
    }

    /**
     * 업그레이드 프롬프트 표시
     */
    public function show_upgrade_prompt() {
        // [Safety Lock] MASTER 또는 PARTNER 버전은 업그레이드 프롬프트 표시 안 함
        if ( class_exists( 'JJ_Edition_Controller' ) && JJ_Edition_Controller::instance()->is_at_least( 'partner' ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'acf-css-really-simple-style-guide' ) === false ) {
            return;
        }

        $license_manager = null;
        $upgrade_url = 'https://j-j-labs.com'; // 기본값
        
        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
            if ( class_exists( 'JJ_License_Manager' ) ) {
                $license_manager = JJ_License_Manager::instance();
                if ( $license_manager ) {
                    $upgrade_url = $license_manager->get_purchase_url( 'upgrade' );
                }
            }
        }
        ?>
        <div class="notice notice-info is-dismissible" style="background: #f0f6fc; border-left-color: #2271b1;">
            <p style="font-size: 14px; line-height: 1.8;">
                <strong><?php _e( '현재 버전을 사용 중입니다.', 'acf-css-really-simple-style-management-center' ); ?></strong><br>
                <?php _e( '더 많은 기능을 이용하려면 업그레이드하세요:', 'acf-css-really-simple-style-management-center' ); ?>
                <a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="button button-primary" style="margin-left: 10px;">
                    <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                </a>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; list-style: disc;">
                <li><?php _e( '모든 팔레트 사용 가능 (브랜드, 시스템, 얼터너티브, 어나더, 임시)', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '모든 테마 및 플러그인 어댑터 지원', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '관리자 센터 및 실험실 센터 전체 기능', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '섹션별 내보내기/불러오기', 'acf-css-really-simple-style-management-center' ); ?></li>
                <li><?php _e( '고급 색상 선택 기능 (스포이드, 팔레트 불러오기)', 'acf-css-really-simple-style-management-center' ); ?></li>
            </ul>
        </div>
        <?php
    }

    public function init_sync_manager() {
        add_action( 'customize_save_after', array( $this, 'sync_from_customizer' ) );
        JJ_Sync_Manager::instance()->init( $this->options, $this->cockpit_page_id );
    }

    /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * Free 버전: 스타일 센터만 표시
     * [v5.1.9] 안전성 강화 - try-catch 추가
     */
            /**
     * [v4.2.2] 플러그인 목록 화면에 링크 추가
     * [Phase 4.99] 퀵 링크 확장: 설정 | 스타일 | 메뉴 | 비주얼
     */
    public function add_plugin_settings_link( $links ) {
        try {
            if ( ! function_exists( 'admin_url' ) || ! function_exists( 'esc_url' ) ) {
                return $links;
            }
            
            $new_links = array();
            
            // 1. 설정 (Admin Center 메인)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center' ) ) . '" style="font-weight: 600;">' . __( 'Settings', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 2. 스타일 (Style Guide)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=acf-css-really-simple-style-guide' ) ) . '">' . __( 'Styles', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 3. 어드민 메뉴 (Admin Menu Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#admin-menu' ) ) . '">' . __( 'Menu', 'acf-css-really-simple-style-management-center' ) . '</a>';
            
            // 4. 비주얼 (Visual Tab)
            $new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=jj-admin-center#visual' ) ) . '">' . __( 'Visual', 'acf-css-really-simple-style-management-center' ) . '</a>';

            // 기존 링크와 병합
            return array_merge( $new_links, $links );
            
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 오류 - ' . $e->getMessage() );
            }
            return $links;
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: add_plugin_settings_link 치명적 오류 - ' . $e->getMessage() );
            }
            return $links;
        }
    }

    public function add_admin_menu_page() {
        add_options_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );
    }

    public function add_admin_menu_links() {
        // [Phase 4.5/4.99] "모양/도구/설정" 어디서나 동일한 화면으로 진입할 수 있도록 서브메뉴 추가
        add_theme_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        add_management_page(
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            __( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ),
            'manage_options',
            JJ_STYLE_GUIDE_PAGE_SLUG,
            array( $this, 'render_style_guide_page' )
        );

        if ( $this->cockpit_page_id ) {
            add_submenu_page(
                'themes.php',
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                __( 'ACF CSS 스타일 설정 페이지', 'acf-css-really-simple-style-management-center' ),
                'manage_options',
                'post.php?post=' . $this->cockpit_page_id . '&action=edit',
                '',
                100
            );
        }
    }

    public function enqueue_admin_scripts( $hook_suffix ) {
        $is_settings_page = in_array(
            $hook_suffix,
            array(
                'settings_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'appearance_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
                'tools_page_' . JJ_STYLE_GUIDE_PAGE_SLUG,
            ),
            true
        );

        $is_cockpit_page = false;
        if ( 'post.php' === $hook_suffix ) {
            global $post;
            if ( $post && (int) $post->ID === $this->cockpit_page_id ) {
                $is_cockpit_page = true;
            }
        }

        if ( ! $is_settings_page && ! $is_cockpit_page ) {
            return;
        }

        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;

        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        // [v5.3.7] Free 버전은 자체 assets 폴더에서 로드
        wp_enqueue_style( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-editor.css', array( 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION );
        wp_enqueue_script( 'jj-style-guide-editor', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-editor.js', array( 'jquery', 'wp-color-picker' ), JJ_STYLE_GUIDE_VERSION, true );
        // [UI/UX] 팔레트 프리셋 + 인라인 프리뷰
        $presets_css_ver = JJ_STYLE_GUIDE_VERSION;
        $presets_js_ver  = JJ_STYLE_GUIDE_VERSION;
        $presets_css_path = JJ_STYLE_GUIDE_PATH . 'assets/css/jj-style-guide-presets.css';
        $presets_js_path  = JJ_STYLE_GUIDE_PATH . 'assets/js/jj-style-guide-presets.js';
        if ( file_exists( $presets_css_path ) ) {
            $presets_css_ver .= '.' . (string) filemtime( $presets_css_path );
        }
        if ( file_exists( $presets_js_path ) ) {
            $presets_js_ver .= '.' . (string) filemtime( $presets_js_path );
        }
        wp_enqueue_style( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/css/jj-style-guide-presets.css', array( 'jj-style-guide-editor' ), $presets_css_ver );
        wp_enqueue_script( 'jj-style-guide-presets', JJ_STYLE_GUIDE_URL . 'assets/js/jj-style-guide-presets.js', array( 'jj-style-guide-editor', 'jquery' ), $presets_js_ver, true );

        $preview_page_id = $this->cockpit_page_id;
        $preview_url = $preview_page_id ? get_permalink( $preview_page_id ) : '';

        wp_localize_script(
            'jj-style-guide-editor',
            'jj_admin_params',
            array(
                'nonce'    => wp_create_nonce( 'jj_style_guide_nonce' ),
                'temp_palette_nonce' => wp_create_nonce( 'jj_temp_palette_nonce' ),
                'settings' => $this->options,
                // AI Extension이 저장한 "추천 팔레트 카드" 목록 (있으면 프리셋 UI에 합류)
                'ai_palette_presets' => (function() {
                    $items = get_option( 'jj_style_guide_ai_palette_presets', array() );
                    return is_array( $items ) ? $items : array();
                })(),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'edition'  => 'free', // Free 버전 표시
                'i18n'     => array(
                    'saving' => __( '저장 중...', 'acf-css-really-simple-style-management-center' ),
                    'saved'  => __( '스타일 저장됨', 'acf-css-really-simple-style-management-center' ),
                    'applied' => __( '임시 팔레트 적용됨! \'스타일 저장\'을 눌러야 최종 반영됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'error'  => __( '오류가 발생했습니다.', 'acf-css-really-simple-style-management-center' ),
                    'confirm_apply' => __( '정말로 \'임시 팔레트\'의 값으로 덮어쓰시겠습니까?\n이 작업은 즉시 UI에 반영되지만, \n\'스타일 저장\' 버튼을 눌러야 최종 저장됩니다.', 'acf-css-really-simple-style-management-center' ),
                    'preview_url' => $preview_url ? esc_url( $preview_url ) : '',
                    'preview_page_id' => $preview_page_id,
                ),
            )
        );
    }

    public function render_style_guide_page() {
        try {
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options      = $options;
            $temp_options       = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY );
            $options            = $this->options;
            $cockpit_page_id    = $this->cockpit_page_id;
            $preview_url        = $cockpit_page_id ? get_permalink( $cockpit_page_id ) : '';

        // 옵션이 비어있으면 기본값 설정
        if ( empty( $this->options ) ) {
            $this->options = array(
                'palettes' => array(
                    'brand' => array(
                        'primary_color' => '',
                        'primary_color_hover' => '',
                        'secondary_color' => '',
                        'secondary_color_hover' => '',
                    ),
                    'system' => array(), // Free 버전: 시스템 팔레트 비활성화
                    'alternative' => array(), // Free 버전: 얼터너티브 팔레트 비활성화
                    'another' => array(), // Free 버전: 어나더 팔레트 비활성화
                ),
                'typography' => array(),
                'buttons' => array(),
                'forms' => array(),
                'contexts' => array(),
            );
            $options = $this->options;
        }
        ?>
        <div id="jj-style-guide-wrapper" class="wrap jj-style-guide-wrap" style="background: #f0f0f1; margin: 0; padding: 0;">
            <form id="jj-style-guide-form">

                <div class="jj-style-guide-header" style="top: 32px; background: #fff; padding: 15px 25px; border-bottom: 1px solid #c3c4c7; z-index: 999; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h1 style="margin: 0; font-size: 24px;">
                            <?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?> (v<?php echo esc_html( JJ_STYLE_GUIDE_VERSION ); ?>) 
                            <?php if ( ! class_exists("JJ_Edition_Controller") || JJ_Edition_Controller::instance()->get_current_edition() === "free" ) : ?><span style="background: #d63638; color: #fff; padding: 4px 8px; border-radius: 3px; font-size: 14px; font-weight: 600;">Free</span><?php endif; ?>
                        </h1>
                        <?php
                        $license_manager = null;
                        $purchase_url = 'https://j-j-labs.com'; // 기본값
                        
                        if ( file_exists( JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php' ) ) {
                            require_once JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-manager.php';
                            if ( class_exists( 'JJ_License_Manager' ) ) {
                                $license_manager = JJ_License_Manager::instance();
                                if ( $license_manager ) {
                                    $purchase_url = $license_manager->get_purchase_url( 'upgrade' );
                                }
                            }
                        }
                        ?>
                        <?php if ( ! class_exists("JJ_Edition_Controller") || ! JJ_Edition_Controller::instance()->is_at_least("partner") ) : ?><a href="<?php echo esc_url( $purchase_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="font-size: 13px; padding: 0 16px; height: 36px; line-height: 34px;">
                            <?php _e( '업그레이드하기', 'acf-css-really-simple-style-management-center' ); ?>
                        </a><?php endif; ?>
                        <?php if ( $preview_url ) : ?>
                        <button type="button" 
                                class="button button-secondary" 
                                id="jj-preview-button"
                                style="font-size: 13px; padding: 0 12px; height: 32px; line-height: 30px;">
                            <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span> <?php _e( '실시간 미리보기', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="jj-header-actions" style="display: flex; align-items: center; gap: 10px;">
                        <span id="jj-save-success" style="display: none; vertical-align: middle; color: #2271b1; font-weight: 600; margin-right: 15px;"></span>
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-save-style-guide" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
                <div class="jj-style-guide-content" style="margin: 20px auto; background: #ffffff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 1px;">
                    <?php
                    // Free 버전: 제한된 섹션만 표시
                    // [v5.3.7] Free 버전은 자체 includes/editor-views 폴더에서 로드
                    $sections_map = array(
                        'colors'     => 'includes/editor-views/view-section-colors.php',
                        'typography' => 'includes/editor-views/view-section-typography.php',
                        'buttons'    => 'includes/editor-views/view-section-buttons.php',
                        'forms'      => 'includes/editor-views/view-section-forms.php',
                    );

                    // Free 버전: 섹션 순서 고정
                    $sections_to_render = array();
                    foreach ( $sections_map as $slug => $relative_path ) {
                        $sections_to_render[] = array(
                            'slug' => $slug,
                            'file' => JJ_STYLE_GUIDE_PATH . $relative_path,
                            'order'=> 0, // 순서는 UI에서만 표시
                        );
                    }
                    ?>
                    <div id="jj-sections-sortable" class="jj-sections-sortable">
                        <?php
                        foreach ( $sections_to_render as $section ) {
                            // Free 버전: 기본 뷰 파일 사용 (제한 로직은 뷰 파일 내부에서 처리)
                            $default_file = JJ_STYLE_GUIDE_PATH . 'includes/editor-views/view-section-' . $section['slug'] . '.php';
                            if ( file_exists( $section['file'] ) ) {
                                echo '<div class="jj-section-wrapper" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $section['file'];
                                echo '</div>';
                            } elseif ( file_exists( $default_file ) ) {
                                // Free 버전 전용 뷰가 없으면 기본 뷰 사용 (제한 로직 적용 필요)
                                echo '<div class="jj-section-wrapper jj-section-free-version" data-section-slug="' . esc_attr( $section['slug'] ) . '">';
                                include $default_file;
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="jj-style-guide-footer" style="margin: 20px auto; padding: 15px 25px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #666; font-size: 13px;">
                        <?php
                        // Master/Partner에서는 업그레이드 유도 문구를 절대 노출하지 않음
                        $show_upgrade_footer = true;
                        if ( class_exists( 'JJ_Edition_Controller' ) ) {
                            try {
                                $show_upgrade_footer = ! JJ_Edition_Controller::instance()->is_at_least( 'partner' );
                            } catch ( Exception $e ) {
                                // ignore
                            } catch ( Error $e ) {
                                // ignore
                            }
                        } elseif ( defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
                            $show_upgrade_footer = ! in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PARTNER', 'MASTER' ), true );
                        }

                        if ( $show_upgrade_footer ) {
                            _e( '더 많은 기능을 이용하려면 업그레이드하세요.', 'acf-css-really-simple-style-management-center' );
                        }
                        ?>
                    </div>
                    <div style="text-align: right;">
                        <span class="spinner" style="float: none; vertical-align: middle; margin: 0 5px 0 0;"></span>
                        <button type="button" class="button button-primary" id="jj-style-guide-footer-button" style="font-size: 14px; padding: 0 16px; height: 40px; line-height: 38px; font-weight: 700;">
                            <?php _e( '스타일 저장', 'acf-css-really-simple-style-management-center' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        } catch ( Exception $e ) {
            // 오류 발생 시에도 기본 페이지 구조는 표시
            ?>
            <div class="wrap" style="padding: 20px;">
                <h1><?php _e( 'ACF CSS 스타일 센터', 'acf-css-really-simple-style-management-center' ); ?></h1>
                <div class="notice notice-error">
                    <p><strong><?php _e( '오류가 발생했습니다:', 'acf-css-really-simple-style-management-center' ); ?></strong> <?php echo esc_html( $e->getMessage() ); ?></p>
                </div>
            </div>
            <?php
        }
    }

    // 나머지 메서드들은 dev 버전과 동일하지만, 기능 제한 로직 추가 필요
    // save_settings_ajax, apply_temp_palette_ajax, save_temp_palette_ajax 등
    
    public function save_settings_ajax() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }
        if ( ! isset( $_POST['settings'] ) || ! is_array( $_POST['settings'] ) ) {
            wp_send_json_error( array( 'message' => __( '잘못된 데이터입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // [Phase 5 B] 멀티사이트 네트워크 전용 모드에서는 사이트별 저장 차단
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $ms = JJ_Multisite_Controller::instance();
                if ( method_exists( $ms, 'is_enabled' ) && method_exists( $ms, 'allow_site_override' ) ) {
                    if ( $ms->is_enabled() && ! $ms->allow_site_override() ) {
                        wp_send_json_error( array( 'message' => __( '이 사이트는 네트워크에서 스타일을 관리 중입니다. 네트워크 관리자에서 변경해주세요.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                }
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        $old_settings = array();
        if ( function_exists( 'get_option' ) ) {
            $old_settings = (array) get_option( $this->options_key, array() );
        }

        $raw_settings = wp_unslash( $_POST['settings'] );
        
        // 에디션별 저장 제한 (Free만 제한, Partner/Master는 무제한)
        $current_edition = 'free';
        if ( class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $current_edition = (string) JJ_Edition_Controller::instance()->get_current_edition();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        } elseif ( defined( 'JJ_STYLE_GUIDE_EDITION' ) ) {
            $current_edition = (string) JJ_STYLE_GUIDE_EDITION;
        }

        if ( 'free' === $current_edition ) {
            $raw_settings = $this->filter_settings_for_free( $raw_settings );
        }
        
        $sanitized_settings = $this->sanitize_options_recursive( $raw_settings );
        update_option( $this->options_key, $sanitized_settings );

        // [Phase 5.2] Webhook/자동화용 이벤트
        if ( function_exists( 'do_action' ) ) {
            do_action( 'jj_style_guide_settings_updated', $sanitized_settings, $old_settings, 'style_center_ajax' );
        }

        $this->sync_to_spokes( $sanitized_settings );

        JJ_CSS_Cache::instance()->flush();
        wp_send_json_success( array( 'message' => __( '설정이 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ) );
    }

    /**
     * Free 버전: 저장 가능한 설정 필터링
     */
    private function filter_settings_for_free( $settings ) {
        // Free 버전: 시스템 팔레트, 얼터너티브 팔레트, 어나더 팔레트 제거
        if ( isset( $settings['palettes'] ) ) {
            unset( $settings['palettes']['system'] );
            unset( $settings['palettes']['alternative'] );
            unset( $settings['palettes']['another'] );
        }
        
        // Free 버전: 타이포그래피 H5, H6 제거
        if ( isset( $settings['typography'] ) ) {
            unset( $settings['typography']['h5'] );
            unset( $settings['typography']['h6'] );
        }
        
        // Free 버전: Secondary, Text 버튼 제거
        if ( isset( $settings['buttons'] ) ) {
            unset( $settings['buttons']['secondary'] );
            unset( $settings['buttons']['text'] );
        }
        
        return $settings;
    }

    public function apply_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $target_palette = isset( $_POST['target_palette'] ) ? sanitize_key( wp_unslash( $_POST['target_palette'] ) ) : 'brand';
        if ( 'brand' !== $target_palette ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 대상 팔레트입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        $temp_palettes = isset( $temp_options['palettes'] ) && is_array( $temp_options['palettes'] ) ? $temp_options['palettes'] : array();
        $temp_brand = isset( $temp_palettes['brand'] ) && is_array( $temp_palettes['brand'] ) ? $temp_palettes['brand'] : array();

        if ( empty( $temp_brand ) ) {
            wp_send_json_error( array( 'message' => __( '임시 팔레트에 저장된 값이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
            $options['palettes'] = array();
        }
        if ( ! isset( $options['palettes']['brand'] ) || ! is_array( $options['palettes']['brand'] ) ) {
            $options['palettes']['brand'] = array();
        }

        $options['palettes']['brand'] = array_merge( (array) $options['palettes']['brand'], (array) $temp_brand );
        update_option( $this->options_key, $options );

        if ( class_exists( 'JJ_CSS_Cache' ) && method_exists( 'JJ_CSS_Cache', 'instance' ) ) {
            try {
                JJ_CSS_Cache::instance()->flush();
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }

        wp_send_json_success( array(
            'message' => __( '임시 팔레트가 브랜드 팔레트에 적용되었습니다.', 'acf-css-really-simple-style-management-center' ),
            'new_settings' => $options,
        ) );
    }

    public function save_temp_palette_ajax() {
        check_ajax_referer( 'jj_temp_palette_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 기능 허용 여부(버전 기능/에디션)
        $can_use_temp = false;
        if ( class_exists( 'JJ_Version_Features' ) ) {
            try {
                $vf = JJ_Version_Features::instance();
                $can_use_temp = $vf ? $vf->can_use_palette( 'temp' ) : false;
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && class_exists( 'JJ_Edition_Controller' ) ) {
            try {
                $can_use_temp = JJ_Edition_Controller::instance()->is_at_least( 'premium' ) || JJ_Edition_Controller::instance()->is_at_least( 'partner' );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        if ( ! $can_use_temp && defined( 'JJ_STYLE_GUIDE_LICENSE_TYPE' ) ) {
            $can_use_temp = in_array( JJ_STYLE_GUIDE_LICENSE_TYPE, array( 'PREMIUM', 'UNLIMITED', 'PARTNER', 'MASTER' ), true );
        }

        if ( ! $can_use_temp ) {
            $message = __( '이 기능은 업그레이드가 필요합니다.', 'acf-css-really-simple-style-management-center' );
            if ( $this->license_manager && method_exists( $this->license_manager, 'get_upgrade_prompt' ) ) {
                $message = $this->license_manager->get_upgrade_prompt( 'palette_temp' );
            }
            wp_send_json_error( array( 'message' => $message ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $brand = isset( $options['palettes']['brand'] ) && is_array( $options['palettes']['brand'] ) ? $options['palettes']['brand'] : array();

        if ( empty( $brand ) ) {
            wp_send_json_error( array( 'message' => __( '브랜드 팔레트 값이 비어 있습니다. 먼저 브랜드 색상을 입력해 주세요.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
        if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
            $temp_options['palettes'] = array();
        }
        $temp_options['palettes']['brand'] = $brand;

        update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );

        wp_send_json_success( array(
            'message' => __( '현재 브랜드 팔레트가 임시 팔레트로 저장되었습니다.', 'acf-css-really-simple-style-management-center' ) ),
            'temp_palettes' => $temp_options['palettes'],
        ) );
    }

    /**
     * [v3.8.0] 섹션별 내보내기
     * - 현재는 팔레트(brand/system/alternative/another/temp-palette)만 지원
     * - 관리자에서 파일 다운로드(attachment)로 전달
     */
    public function ajax_export_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_die( esc_html__( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_die( esc_html__( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) );
        }

        $options = (array) get_option( $this->options_key, array() );
        $data = array();

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            $data = isset( $temp_options['palettes'] ) ? (array) $temp_options['palettes'] : array();
        } else {
            $data = isset( $options['palettes'][ $section_subtype ] ) ? (array) $options['palettes'][ $section_subtype ] : array();
        }

        $payload = array(
            'exported_at'   => function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'c' ),
            'plugin'        => 'acf-css-really-simple-style-management-center',
            'plugin_version'=> defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '',
            'section_type'  => $section_type,
            'section_subtype' => $section_subtype,
            'data'          => $data,
        );

        if ( function_exists( 'nocache_headers' ) ) {
            nocache_headers();
        }

        $filename = sprintf(
            'acf-css-%s-%s-%s.json',
            $section_type,
            $section_subtype,
            gmdate( 'Ymd-His' )
        );

        // 다운로드 헤더
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

        echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        exit;
    }

    /**
     * [v3.8.0] 섹션별 불러오기
     * - JSON 업로드 후 옵션에 병합(키 기준 덮어쓰기)
     */
    public function ajax_import_section() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $section_type = isset( $_POST['section_type'] ) ? sanitize_key( wp_unslash( $_POST['section_type'] ) ) : '';
        $section_subtype = isset( $_POST['section_subtype'] ) ? sanitize_key( wp_unslash( $_POST['section_subtype'] ) ) : '';

        if ( 'palette' !== $section_type ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed_subtypes = array( 'brand', 'system', 'alternative', 'another', 'temp' );
        if ( ! in_array( $section_subtype, $allowed_subtypes, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 섹션 서브타입입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        if ( empty( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) ) {
            wp_send_json_error( array( 'message' => __( '업로드된 파일이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $tmp_name = $_FILES['import_file']['tmp_name'];
        $raw = file_get_contents( $tmp_name );
        if ( ! $raw ) {
            wp_send_json_error( array( 'message' => __( '파일을 읽을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $decoded = json_decode( $raw, true );
        if ( ! is_array( $decoded ) ) {
            wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // payload 형식({data:{...}})과 순수 데이터({..}) 둘 다 허용
        $import_data = isset( $decoded['data'] ) && is_array( $decoded['data'] ) ? (array) $decoded['data'] : $decoded;

        // 간단 정리(색상 키는 hex로)
        $sanitized = array();
        foreach ( (array) $import_data as $k => $v ) {
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( (string) $k ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $k ) );
            if ( is_array( $v ) ) {
                // 팔레트는 1depth만 정리 (필요 시 확장)
                $sanitized[ $key ] = $v;
                continue;
            }
            if ( strpos( $key, 'color' ) !== false ) {
                $hex = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( (string) $v ) : '';
                $sanitized[ $key ] = $hex ? $hex : '';
                continue;
            }
            $sanitized[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $v ) : strip_tags( (string) $v );
        }

        if ( 'temp' === $section_subtype ) {
            $temp_options = (array) get_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, array() );
            if ( ! isset( $temp_options['palettes'] ) || ! is_array( $temp_options['palettes'] ) ) {
                $temp_options['palettes'] = array();
            }
            $temp_options['palettes'] = array_merge( (array) $temp_options['palettes'], $sanitized );
            update_option( JJ_STYLE_GUIDE_TEMP_OPTIONS_KEY, $temp_options );
        } else {
            $options = (array) get_option( $this->options_key, array() );
            if ( ! isset( $options['palettes'] ) || ! is_array( $options['palettes'] ) ) {
                $options['palettes'] = array();
            }
            $options['palettes'][ $section_subtype ] = array_merge(
                (array) ( $options['palettes'][ $section_subtype ] ?? array() ),
                $sanitized
            );
            update_option( $this->options_key, $options );
        }

        wp_send_json_success( array(
            'message' => __( '설정이 성공적으로 불러와졌습니다.', 'acf-css-really-simple-style-management-center' ),
        ) );
    }

    /**
     * AI 저장 추천 팔레트 관리 (핀/삭제/이름변경)
     *
     * 요청:
     * - action: jj_ai_palette_preset_manage
     * - security: jj_style_guide_nonce
     * - op: pin|unpin|delete|rename
     * - id: preset id (ai- 로 시작)
     * - name: (rename 시) 새 이름
     *
     * @return void
     */
    public function ajax_ai_palette_preset_manage() {
        check_ajax_referer( 'jj_style_guide_nonce', 'security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( '권한이 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $op = isset( $_POST['op'] ) ? sanitize_key( wp_unslash( $_POST['op'] ) ) : '';
        $id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
        $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

        if ( '' === $op || '' === $id ) {
            wp_send_json_error( array( 'message' => __( '요청 파라미터가 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 안전: AI 프리셋만 조작
        if ( 0 !== strpos( $id, 'ai-' ) ) {
            wp_send_json_error( array( 'message' => __( 'AI 프리셋만 관리할 수 있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $allowed = array( 'pin', 'unpin', 'delete', 'rename', 'import' );
        if ( ! in_array( $op, $allowed, true ) ) {
            wp_send_json_error( array( 'message' => __( '지원하지 않는 작업입니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        $option_key = 'jj_style_guide_ai_palette_presets';
        $items = get_option( $option_key, array() );
        if ( ! is_array( $items ) ) {
            $items = array();
        }

        // Import: JSON payload -> presets append (dedupe)
        if ( 'import' === $op ) {
            $payload_json = isset( $_POST['payload_json'] ) ? wp_unslash( $_POST['payload_json'] ) : '';
            if ( '' === $payload_json ) {
                wp_send_json_error( array( 'message' => __( '불러올 JSON이 비어있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            $payload = json_decode( $payload_json, true );
            if ( null === $payload ) {
                wp_send_json_error( array( 'message' => __( 'JSON 파싱에 실패했습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            $incoming = array();
            if ( is_array( $payload ) && isset( $payload[0] ) ) {
                $incoming = $payload;
            } elseif ( is_array( $payload ) ) {
                $incoming = array( $payload );
            } else {
                wp_send_json_error( array( 'message' => __( 'JSON 형식이 올바르지 않습니다.', 'acf-css-really-simple-style-management-center' ) ) );
            }

            $sanitize_hex = function( $value, $fallback = '' ) {
                $v = is_string( $value ) ? $value : '';
                $v = trim( $v );
                if ( '' === $v ) {
                    $v = (string) $fallback;
                }
                if ( function_exists( 'sanitize_hex_color' ) ) {
                    $hex = sanitize_hex_color( $v );
                    return $hex ? strtoupper( $hex ) : '';
                }
                return $v;
            };

            // existing signatures to dedupe
            $sigs = array();
            foreach ( $items as $ex ) {
                if ( ! is_array( $ex ) ) {
                    continue;
                }
                $b = isset( $ex['brand'] ) && is_array( $ex['brand'] ) ? $ex['brand'] : array();
                $s = isset( $ex['system'] ) && is_array( $ex['system'] ) ? $ex['system'] : array();
                $sig = md5(
                    (string) ( $b['primary'] ?? '' ) . '|' .
                    (string) ( $b['secondary'] ?? '' ) . '|' .
                    (string) ( $s['site_bg'] ?? '' ) . '|' .
                    (string) ( $s['content_bg'] ?? '' ) . '|' .
                    (string) ( $s['text_color'] ?? '' ) . '|' .
                    (string) ( $s['link_color'] ?? '' )
                );
                $sigs[ $sig ] = true;
            }

            $imported = 0;
            $skipped = 0;

            foreach ( $incoming as $row ) {
                if ( ! is_array( $row ) ) {
                    $skipped++;
                    continue;
                }

                $name_in = isset( $row['name'] ) ? sanitize_text_field( (string) $row['name'] ) : '';
                if ( '' === $name_in ) {
                    $name_in = __( '가져온 AI 팔레트', 'acf-css-really-simple-style-management-center' );
                }
                if ( function_exists( 'mb_substr' ) ) {
                    $name_in = mb_substr( $name_in, 0, 80 );
                } else {
                    $name_in = substr( $name_in, 0, 80 );
                }

                $note_in = isset( $row['note'] ) ? sanitize_text_field( (string) $row['note'] ) : '';
                if ( function_exists( 'mb_substr' ) ) {
                    $note_in = mb_substr( $note_in, 0, 220 );
                } else {
                    $note_in = substr( $note_in, 0, 220 );
                }

                $tags_in = array();
                if ( isset( $row['tags'] ) && is_array( $row['tags'] ) ) {
                    foreach ( $row['tags'] as $t ) {
                        $ts = sanitize_text_field( (string) $t );
                        if ( '' !== $ts ) {
                            $tags_in[] = $ts;
                        }
                    }
                }
                if ( ! in_array( 'AI', $tags_in, true ) ) {
                    array_unshift( $tags_in, 'AI' );
                }
                $tags_in = array_values( array_unique( $tags_in ) );
                $tags_in = array_slice( $tags_in, 0, 8 );

                $brand_in = isset( $row['brand'] ) && is_array( $row['brand'] ) ? $row['brand'] : array();
                $system_in = isset( $row['system'] ) && is_array( $row['system'] ) ? $row['system'] : array();

                $primary = $sanitize_hex( $brand_in['primary'] ?? '' );
                if ( '' === $primary ) {
                    $skipped++;
                    continue;
                }
                $secondary = $sanitize_hex( $brand_in['secondary'] ?? '', $primary );

                $site_bg = $sanitize_hex( $system_in['site_bg'] ?? '' );
                $content_bg = $sanitize_hex( $system_in['content_bg'] ?? '' );
                $text_color = $sanitize_hex( $system_in['text_color'] ?? '' );
                $link_color = $sanitize_hex( $system_in['link_color'] ?? '', $primary );

                $sig2 = md5( $primary . '|' . $secondary . '|' . $site_bg . '|' . $content_bg . '|' . $text_color . '|' . $link_color );
                if ( isset( $sigs[ $sig2 ] ) ) {
                    $skipped++;
                    continue;
                }
                $sigs[ $sig2 ] = true;

                $new_id = 'ai-' . gmdate( 'Ymd-His' ) . '-' . substr( md5( wp_generate_uuid4() . microtime( true ) ), 0, 8 );
                $created_at = function_exists( 'current_time' ) ? current_time( 'mysql' ) : gmdate( 'Y-m-d H:i:s' );

                $new_item = array(
                    'id' => $new_id,
                    'name' => $name_in,
                    'tags' => $tags_in,
                    'note' => $note_in,
                    'brand' => array(
                        'primary' => $primary,
                        'secondary' => $secondary,
                        'primary_hover' => $sanitize_hex( $brand_in['primary_hover'] ?? '', $primary ),
                        'secondary_hover' => $sanitize_hex( $brand_in['secondary_hover'] ?? '', $secondary ),
                    ),
                    'system' => array(
                        'site_bg' => $site_bg,
                        'content_bg' => $content_bg,
                        'text_color' => $text_color,
                        'link_color' => $link_color,
                    ),
                    'source' => 'ai_extension',
                    'created_at' => $created_at,
                    'pinned' => ! empty( $row['pinned'] ),
                );

                array_unshift( $items, $new_item );
                $imported++;
            }

            // 정렬: pinned 우선, 최신(created_at) 우선
            usort(
                $items,
                function( $a, $b ) {
                    $ap = ! empty( $a['pinned'] );
                    $bp = ! empty( $b['pinned'] );
                    if ( $ap !== $bp ) {
                        return $ap ? -1 : 1;
                    }
                    $at = (string) ( $a['created_at'] ?? '' );
                    $bt = (string) ( $b['created_at'] ?? '' );
                    if ( $at === $bt ) return 0;
                    return ( $bt < $at ) ? -1 : 1; // desc
                }
            );

            $items = array_slice( $items, 0, 20 );
            update_option( $option_key, $items, false );

            wp_send_json_success(
                array(
                    'message' => sprintf( __( '가져오기 완료: %d개 추가, %d개 건너뜀', 'acf-css-really-simple-style-management-center' ), $imported, $skipped ),
                    'items' => $items,
                )
            );
        }

        $found = false;
        if ( 'delete' === $op ) {
            $before = count( $items );
            $items = array_values(
                array_filter(
                    $items,
                    function( $p ) use ( $id ) {
                        return is_array( $p ) && ( (string) ( $p['id'] ?? '' ) !== (string) $id );
                    }
                )
            );
            $found = ( $before !== count( $items ) );
        } else {
            foreach ( $items as &$p ) {
                if ( ! is_array( $p ) ) {
                    continue;
                }
                if ( (string) ( $p['id'] ?? '' ) !== (string) $id ) {
                    continue;
                }
                $found = true;

                if ( 'pin' === $op ) {
                    $p['pinned'] = true;
                } elseif ( 'unpin' === $op ) {
                    $p['pinned'] = false;
                } elseif ( 'rename' === $op ) {
                    if ( '' === $name ) {
                        wp_send_json_error( array( 'message' => __( '이름이 비어있습니다.', 'acf-css-really-simple-style-management-center' ) ) );
                    }
                    $p['name'] = $name;
                }
                break;
            }
            unset( $p );
        }

        if ( ! $found ) {
            wp_send_json_error( array( 'message' => __( '해당 프리셋을 찾을 수 없습니다.', 'acf-css-really-simple-style-management-center' ) ) );
        }

        // 정렬: pinned 우선, 최신(created_at) 우선
        usort(
            $items,
            function( $a, $b ) {
                $ap = ! empty( $a['pinned'] );
                $bp = ! empty( $b['pinned'] );
                if ( $ap !== $bp ) {
                    return $ap ? -1 : 1;
                }
                $at = (string) ( $a['created_at'] ?? '' );
                $bt = (string) ( $b['created_at'] ?? '' );
                if ( $at === $bt ) return 0;
                return ( $bt < $at ) ? -1 : 1; // desc
            }
        );

        // 최대 20개 유지
        $items = array_slice( $items, 0, 20 );

        update_option( $option_key, $items, false );

        $msg = __( '완료되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'delete' === $op ) $msg = __( '삭제되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'pin' === $op ) $msg = __( '상단 고정되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'unpin' === $op ) $msg = __( '고정이 해제되었습니다.', 'acf-css-really-simple-style-management-center' );
        if ( 'rename' === $op ) $msg = __( '이름이 변경되었습니다.', 'acf-css-really-simple-style-management-center' );

        wp_send_json_success(
            array(
                'message' => $msg,
                'items' => $items,
            )
        );
    }

    // 나머지 메서드들은 dev 버전과 동일 (sync_to_spokes, sync_from_customizer, sanitize_options_recursive 등)
    // 생략하거나 dev 버전 파일을 복사하여 수정

    private function sync_to_spokes( $hub_options ) {
        // Free 버전: WooCommerce만 동기화
        $brand_colors = $hub_options['palettes']['brand'] ?? array();
        
        // WooCommerce 동기화
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! is_array( $kwe ) ) {
            $kwe = array();
        }
        if ( isset( $brand_colors['primary_color'] ) ) {
            $kwe['product_quickview_button_color'] = $brand_colors['primary_color'];
        }
        if ( isset( $brand_colors['primary_color_hover'] ) ) {
            $kwe['product_quickview_button_color_hover'] = $brand_colors['primary_color_hover'];
        }
        update_option( 'kt_woo_extras', $kwe );
    }

    public function sync_from_customizer( $manager ) {
        $hub_options = (array) get_option( $this->options_key );
        $panel_settings = $manager->settings();
        
        foreach ( $panel_settings as $setting_id => $setting_obj ) {
            if ( strpos( $setting_id, $this->options_key . '[' ) === 0 ) {
                $keys = str_replace( array( $this->options_key, '[', ']' ), '', $setting_id );
                $keys = explode( '][', $keys );
                $this->set_nested_object_value( $hub_options, $keys, $setting_obj->post_value() );
            }
        }

        update_option( $this->options_key, $hub_options );
        JJ_CSS_Cache::instance()->flush();
    }

    private function set_nested_object_value( &$obj, $keys, $value ) {
        $current = &$obj;
        foreach ( $keys as $key ) {
            if ( ! isset( $current[$key] ) || ! is_array( $current[$key] ) ) {
                $current[$key] = array();
            }
            $current = &$current[$key];
        }
        $current = $value;
    }

    private function sanitize_options_recursive( $input ) {
        $output = array();
        foreach ( (array) $input as $key => $value ) {
            $raw_key = (string) $key;
            // [v5.3.8] WordPress 함수 안전 호출
            $key = function_exists( 'sanitize_key' ) ? sanitize_key( $raw_key ) : preg_replace( '/[^a-z0-9_-]/', '', strtolower( $raw_key ) );
            if ( is_array( $value ) ) {
                $output[ $key ] = $this->sanitize_options_recursive( $value );
                continue;
            }
            // [v5.3.8] PHP 7.4 호환성: str_contains() 대신 strpos() 사용
            if ( strpos( $key, 'color' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $sanitized = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( $value ) : '';
                $output[ $key ] = $sanitized ? $sanitized : '';
                continue;
            }
            if ( strpos( $key, 'custom_css' ) !== false || strpos( $key, 'additional_css' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'wp_kses_post' ) ? wp_kses_post( $value ) : wp_strip_all_tags( $value );
                continue;
            }
            if ( strpos( $key, 'font_family' ) !== false ) {
                // [v5.3.8] WordPress 함수 안전 호출
                $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
                continue;
            }
            // [v5.3.8] WordPress 함수 안전 호출
            $output[ $key ] = function_exists( 'sanitize_text_field' ) ? sanitize_text_field( $value ) : strip_tags( $value );
        }
        return $output;
    }

    public function load_options_and_init_strategies() {
        // [v5.3.5] 전체 메서드를 try-catch로 보호하여 치명적 오류 방지
        try {
            $this->environment_map = $this->scan_environment();
            $this->load_dynamic_adapters( $this->environment_map );
            $options = (array) get_option( $this->options_key );
            // [Phase 5 B] Multisite 네트워크 기본값 적용
            if ( class_exists( 'JJ_Multisite_Controller' ) ) {
                try {
                    $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
                } catch ( Exception $e ) {
                    // ignore
                } catch ( Error $e ) {
                    // ignore
                }
            }
            $this->options = $options;
            $this->sync_existing_settings();
            $this->brand_colors = $this->options['palettes']['brand'] ?? array();

            // [v5.3.5] 모든 전략 클래스 호출에 class_exists() 확인 추가
            // 전략 1: CSS 변수
            if ( class_exists( 'JJ_Strategy_1_CSS_Vars' ) ) {
                JJ_Strategy_1_CSS_Vars::instance()->init( $this->options );
            }
            
            // 전략 2: PHP 필터
            if ( class_exists( 'JJ_Strategy_2_PHP_Filters' ) ) {
                JJ_Strategy_2_PHP_Filters::instance()->init( $this->options, null, null, null, null );
            }
            
            // 전략 3: Dequeue (CSS 재정의)
            if ( class_exists( 'JJ_Strategy_3_Dequeue' ) ) {
                JJ_Strategy_3_Dequeue::instance()->init( $this->options );
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Free: 전략 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    private function sync_existing_settings() {
        $active_theme = wp_get_theme()->get_template();
        $needs_update = false;

        if ( ! isset( $this->options['palettes'] ) ) {
            $this->options['palettes'] = array();
        }
        if ( ! isset( $this->options['palettes']['brand'] ) ) {
            $this->options['palettes']['brand'] = array();
        }
        if ( ! isset( $this->options['typography'] ) ) {
            $this->options['typography'] = array();
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five 테마만 동기화
        if ( in_array( $active_theme, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
            // 기본 테마는 별도 설정이 없으므로 Customizer에서 가져오기
        }

        // WooCommerce 동기화 (Free 버전 지원)
        $kwe = get_option( 'kt_woo_extras', array() );
        if ( ! empty( $kwe['product_quickview_button_color'] ) && empty( $this->options['palettes']['brand']['primary_color'] ) ) {
            $this->options['palettes']['brand']['primary_color'] = $kwe['product_quickview_button_color'];
            $needs_update = true;
        }
        if ( ! empty( $kwe['product_quickview_button_color_hover'] ) && empty( $this->options['palettes']['brand']['primary_color_hover'] ) ) {
            $this->options['palettes']['brand']['primary_color_hover'] = $kwe['product_quickview_button_color_hover'];
            $needs_update = true;
        }

        if ( $needs_update ) {
            update_option( $this->options_key, $this->options );
        }
    }

    private function load_dynamic_adapters( $environment_map ) {
        $active_adapters = $environment_map;
        $options = (array) get_option( $this->options_key );
        // [Phase 5 B] Multisite 네트워크 기본값 적용
        if ( class_exists( 'JJ_Multisite_Controller' ) ) {
            try {
                $options = JJ_Multisite_Controller::instance()->filter_site_options( $options );
            } catch ( Exception $e ) {
                // ignore
            } catch ( Error $e ) {
                // ignore
            }
        }
        $this->options = $options;
        $error_handler = JJ_Error_Handler::instance();

        // Free 버전: 테마 어댑터 제한 (Twenty Twenty-Four, Twenty Twenty-Five만)
        if ( ! empty( $active_adapters['themes'] ) ) {
            $allowed_themes = array( 'twentytwentyfour', 'twentytwentyfive' );
            $active_adapters['themes'] = array_intersect( $active_adapters['themes'], $allowed_themes );
            
            foreach ( $active_adapters['themes'] as $theme_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-theme-' . sanitize_file_name( $theme_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Theme_' . str_replace( '-', '_', ucwords( $theme_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }

        // Free 버전: 플러그인 어댑터 제한 (WooCommerce만)
        if ( ! empty( $active_adapters['spokes'] ) ) {
            $allowed_plugins = array( 'woocommerce' );
            $active_adapters['spokes'] = array_intersect( $active_adapters['spokes'], $allowed_plugins );
            
            foreach ( $active_adapters['spokes'] as $spoke_slug ) {
                // [v5.3.7] Free 버전은 자체 adapters 폴더에서 로드
                $adapter_file = JJ_STYLE_GUIDE_PATH . 'adapters/adapter-spoke-' . sanitize_file_name( $spoke_slug ) . '.php';
                if ( file_exists( $adapter_file ) ) {
                    require_once $adapter_file;
                    $class_name = 'JJ_Adapter_Spoke_' . str_replace( '-', '_', ucwords( $spoke_slug, '-' ) );
                    if ( class_exists( $class_name ) && method_exists( $class_name, 'instance' ) ) {
                        $error_handler->safe_execute(
                            array( call_user_func( array( $class_name, 'instance' ) ), 'init' ),
                            array( $this->options ),
                            null
                        );
                    }
                }
            }
        }
    }

    private function scan_environment() {
        $config = $this->adapters_config;
        $spokes_to_check = $config['spokes'] ?? array();
        $themes_to_check = $config['themes'] ?? array();

        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        $active_spoke_adapters = array();
        $active_theme_adapters = array();
        $current_theme_path = get_template_directory();
        $theme_dir_name = basename( $current_theme_path );

        // Free 버전: WooCommerce만 체크
        foreach ( $spokes_to_check as $adapter_slug => $plugin_path ) {
            if ( in_array( $plugin_path, $active_plugins ) && $adapter_slug === 'woocommerce' ) {
                $active_spoke_adapters[] = $adapter_slug;
            }
        }

        // Free 버전: Twenty Twenty-Four, Twenty Twenty-Five만 체크
        foreach ( $themes_to_check as $adapter_slug => $theme_path_suffix ) {
            if ( $theme_dir_name === $adapter_slug && in_array( $adapter_slug, array( 'twentytwentyfour', 'twentytwentyfive' ) ) ) {
                if ( file_exists( $current_theme_path . '/' . $theme_path_suffix ) ) {
                    $active_theme_adapters[] = $adapter_slug;
                }
            }
        }

        return array(
            'themes' => $active_theme_adapters,
            'spokes' => $active_spoke_adapters,
        );
    }

    public function render_debug_info() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $errors = JJ_Error_Handler::instance()->get_errors();

        echo '<div style="background: #001a33; color: #00ff8c; padding: 20px; font-family: monospace; font-size: 14px; line-height: 1.6; z-index: 99999; position: relative; margin: 20px; border-radius: 5px; white-space: pre-wrap; word-break: break-all; opacity: 0.9;">';
        echo '<strong>[ACF CSS DEBUG - FREE VERSION]</strong><br>';
        echo '====================<br>';
        echo '<strong>Edition:</strong> Free<br>';
        echo '<strong>Loaded Adapters:</strong><br>';
        if ( ! empty( $this->environment_map['themes'] ) ) {
            echo ' - Themes: ' . esc_html( implode( ', ', $this->environment_map['themes'] ) ) . '<br>';
        } else {
            echo ' - Themes: None<br>';
        }
        if ( ! empty( $this->environment_map['spokes'] ) ) {
            echo ' - Spokes: ' . esc_html( implode( ', ', $this->environment_map['spokes'] ) ) . '<br>';
        } else {
            echo ' - Spokes: None<br>';
        }
        echo '</div>';
    }
}

function jj_simple_style_guide_master_run() {
    // [Phase 5] 다국어 지원 로드
    load_plugin_textdomain( 'acf-css-really-simple-style-management-center', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // WordPress 함수가 로드되었는지 확인
    if ( ! function_exists( 'class_exists' ) || ! function_exists( 'file_exists' ) ) {
        return;
    }
    
    // [v5.3.9] 코드 무결성 모니터 초기화 (plugins_loaded 시점)
    
    // [Phase 4.5/4.99] Admin Center 초기화 (메뉴/상단바/관리자 UI)
    if ( class_exists( 'JJ_Admin_Center' ) ) {
        try {
            $admin_center = JJ_Admin_Center::instance();
            if ( method_exists( $admin_center, 'init' ) ) {
                $admin_center->init();
            }
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Admin Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v3.7.0] 백업 관리자 초기화
    if ( class_exists( 'JJ_Backup_Manager' ) ) {
        try {
            JJ_Backup_Manager::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Backup Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] REST API 초기화
    if ( class_exists( 'JJ_Style_Guide_REST_API' ) ) {
        try {
            JJ_Style_Guide_REST_API::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ REST API: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.3] Extension Manager 초기화
    if ( class_exists( 'JJ_Extension_Manager' ) ) {
        try {
            JJ_Extension_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Extension Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5.2] Webhook Manager 초기화
    if ( class_exists( 'JJ_Webhook_Manager' ) ) {
        try {
            JJ_Webhook_Manager::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Webhook Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [Phase 5 B] Multisite Controller 초기화
    if ( class_exists( 'JJ_Multisite_Controller' ) ) {
        try {
            JJ_Multisite_Controller::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Multisite Controller: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }

    // [v5.6.0] Visual Command Center 초기화
    if ( class_exists( 'JJ_Visual_Command_Center' ) ) {
        JJ_Visual_Command_Center::instance();
    }

    // [v5.7.0] AI Style Intelligence 초기화
    if ( class_exists( 'JJ_Smart_Palette' ) ) {
        JJ_Smart_Palette::instance();
    }

    // [v5.9.0] CSS Optimizer 초기화
    if ( class_exists( 'JJ_CSS_Optimizer' ) ) {
        JJ_CSS_Optimizer::instance();
    }

    // [v5.8.0] Cloud Manager 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Cloud_Manager' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) { // Premium 이상 체크 대용
            JJ_Cloud_Manager::instance();
        }
    }
    
    // [Phase 3] 템플릿 마켓 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Template_Market' ) && class_exists( 'JJ_Edition_Controller' ) ) {
        if ( JJ_Edition_Controller::instance()->has_capability( 'all_adapters' ) ) {
            JJ_Template_Market::instance();
        }
    }
    
    // [Phase 5] Partner Hub 초기화 (Partner/Master만)
    if ( class_exists( 'JJ_Partner_Hub' ) ) {
        JJ_Partner_Hub::instance();
    }

    // [v4.2.2] 실험실(Labs) 초기화 (Premium 이상)
    if ( class_exists( 'JJ_Labs_Center' ) ) {
        try {
            JJ_Labs_Center::instance()->init();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Labs Center: 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }


    if ( class_exists( 'JJ_Code_Integrity_Monitor' ) ) {
        try {
            JJ_Code_Integrity_Monitor::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'JJ Code Integrity Monitor: 초기화 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.9] 라이센스 강제 실행 초기화 (plugins_loaded 시점)
    if ( defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $enforcement_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-license-enforcement.php';
        if ( file_exists( $enforcement_file ) ) {
            require_once $enforcement_file;
            if ( class_exists( 'JJ_License_Enforcement' ) ) {
                try {
                    JJ_License_Enforcement::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ License Enforcement: 초기화 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // [v5.3.7] 안전 모드 플래그 확인 및 활성화
    if ( function_exists( 'get_option' ) && class_exists( 'JJ_Safe_Mode' ) ) {
        $safe_mode_enabled = get_option( 'jj_style_guide_safe_mode', false );
        if ( $safe_mode_enabled ) {
            $safe_mode_reason = get_option( 'jj_style_guide_safe_mode_reason', '활성화 중 오류 발생' );
            try {
                $safe_mode = JJ_Safe_Mode::instance();
                if ( method_exists( $safe_mode, 'enable' ) ) {
                    $safe_mode->enable( $safe_mode_reason );
                }
            } catch ( Exception $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 오류 - ' . $e->getMessage() );
                }
            } catch ( Error $e ) {
                if ( function_exists( 'error_log' ) ) {
                    error_log( 'ACF CSS Manager: 안전 모드 활성화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
    
    // 메인 클래스 인스턴스 생성
    if ( class_exists( 'JJ_Simple_Style_Guide_Master' ) ) {
        try {
            JJ_Simple_Style_Guide_Master::instance();
        } catch ( Exception $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 오류 - ' . $e->getMessage() );
            }
        } catch ( Error $e ) {
            if ( function_exists( 'error_log' ) ) {
                error_log( 'ACF CSS Manager: 메인 클래스 초기화 치명적 오류 - ' . $e->getMessage() );
            }
        }
    }
    
    // [v5.3.6] 기존 플러그인 마이그레이션 매니저 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $migration_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-migration-manager.php';
        if ( file_exists( $migration_file ) ) {
            // 마이그레이션 매니저는 이미 safe_loader를 통해 로드되었을 수 있음
            // 클래스가 존재하는 경우에만 초기화
            if ( class_exists( 'JJ_Migration_Manager' ) ) {
                try {
                    JJ_Migration_Manager::instance();
                } catch ( Exception $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 오류 - ' . $e->getMessage() );
                    }
                } catch ( Error $e ) {
                    if ( function_exists( 'error_log' ) ) {
                        error_log( 'JJ Migration Manager: 초기화 치명적 오류 - ' . $e->getMessage() );
                    }
                }
            }
        }
    }
    
    // 플러그인 업데이트 체크 초기화 (WordPress가 완전히 로드된 후)
    if ( function_exists( 'file_exists' ) && defined( 'JJ_STYLE_GUIDE_PATH' ) ) {
        $updater_file = JJ_STYLE_GUIDE_PATH . 'includes/class-jj-plugin-updater.php';
        if ( file_exists( $updater_file ) ) {
            require_once $updater_file;
            if ( class_exists( 'JJ_Plugin_Updater' ) ) {
                try {
                    new JJ_Plugin_Updater(
                        __FILE__,
                        'advanced-custom-style-manage-center',
                        "ACF CSS Master",
                        defined( 'JJ_STYLE_GUIDE_VERSION' ) ? JJ_STYLE_GUIDE_VERSION : '5.4.0',
                        'DEV' // 마스터 버전
                    );
                } catch ( Exception $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 오류 - ' . $e->getMessage() );
                } catch ( Error $e ) {
                    error_log( 'JJ Plugin Updater: 초기화 치명적 오류 - ' . $e->getMessage() );
                }
            }
        }
    }
}

// [v5.3.8] plugins_loaded 훅 활성화
if ( function_exists( 'add_action' ) ) {
    add_action( 'plugins_loaded', 'jj_simple_style_guide_master_run', 10 );
}

/**
 * [v8.0.0] 안전 장치: 필수 헬퍼 함수 Fallback 정의
 * class-jj-admin-center.php 로드 실패 시에도 사이트가 멈추지 않도록 보장합니다.
 */
if ( ! function_exists( 'jj_style_guide_text' ) ) {
    function jj_style_guide_text( $key, $fallback = '' ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_text( $key, $fallback );
        }
        return $fallback;
    }
}

if ( ! function_exists( 'jj_style_guide_sections_layout' ) ) {
    function jj_style_guide_sections_layout() {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_sections_layout();
        }
        return array();
    }
}

if ( ! function_exists( 'jj_style_guide_section_index' ) ) {
    function jj_style_guide_section_index( $slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->get_section_index( $slug );
        }
        return null;
    }
}

if ( ! function_exists( 'jj_style_guide_is_tab_enabled' ) ) {
    function jj_style_guide_is_tab_enabled( $section_slug, $tab_slug ) {
        if ( class_exists( 'JJ_Admin_Center' ) ) {
            return JJ_Admin_Center::instance()->is_tab_enabled( $section_slug, $tab_slug );
        }
        return true;
    }
}

```

```javascript
/**
 * JJ Style Guide - Palette Presets & Inline Preview
 *
 * 목표:
 * - 텍스트 위주의 팔레트 UI를 "보여주는" UI로 보강
 * - 2-Color 조합(Primary/Secondary) + 권장 System 팔레트까지 프리셋으로 제공
 * - 클릭 1~2번으로 즉시 적용 + 즉시 미리보기
 *
 * 주의:
 * - 기존 `jj-style-guide-editor.js`의 currentSettings/필드 바인딩 로직을 존중합니다.
 * - 실제 값 적용은 `data-setting-key` 필드 값을 변경(trigger change)하는 방식으로 통일합니다.
 */
(function($){
  'use strict';

  function normalizeHex(hex) {
    if (!hex || typeof hex !== 'string') return '';
    var h = hex.trim();
    if (!h) return '';
    if (h[0] !== '#') h = '#' + h;
    if (h.length === 4) {
      // #RGB -> #RRGGBB
      h = '#' + h[1] + h[1] + h[2] + h[2] + h[3] + h[3];
    }
    return h.toUpperCase();
  }

  function clamp(n, min, max) { return Math.max(min, Math.min(max, n)); }

  function hexToRgb(hex) {
    var h = normalizeHex(hex);
    if (!h || h.length !== 7) return null;
    return {
      r: parseInt(h.slice(1, 3), 16),
      g: parseInt(h.slice(3, 5), 16),
      b: parseInt(h.slice(5, 7), 16),
    };
  }

  function rgbToHex(r, g, b) {
    var rr = clamp(Math.round(r), 0, 255).toString(16).padStart(2, '0');
    var gg = clamp(Math.round(g), 0, 255).toString(16).padStart(2, '0');
    var bb = clamp(Math.round(b), 0, 255).toString(16).padStart(2, '0');
    return ('#' + rr + gg + bb).toUpperCase();
  }

  // amount: -1.0 ~ +1.0 (음수: 어둡게, 양수: 밝게)
  function shade(hex, amount) {
    var rgb = hexToRgb(hex);
    if (!rgb) return '';
    var t = amount < 0 ? 0 : 255;
    var p = Math.abs(amount);
    return rgbToHex(
      (t - rgb.r) * p + rgb.r,
      (t - rgb.g) * p + rgb.g,
      (t - rgb.b) * p + rgb.b
    );
  }

  function setField(settingKey, value) {
    if (!settingKey) return;
    var v = normalizeHex(value);
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    if (!$field.length) return;

    $field.val(v).trigger('change');
    // wpColorPicker가 있으면 색상도 동기화
    if ($field.closest('.wp-picker-container').length) {
      try { $field.wpColorPicker('color', v); } catch (e) {}
    }
    // 카드 미리보기(기존 DOM)
    var $preview = $field.closest('.jj-control-group').find('.jj-color-preview').first();
    if ($preview.length) $preview.css('background-color', v);
  }

  function readField(settingKey, fallback) {
    var $field = $('#jj-style-guide-form').find('[data-setting-key="' + settingKey + '"]');
    var v = $field.length ? normalizeHex($field.val()) : '';
    return v || normalizeHex(fallback) || '';
  }

  function updateInlinePreview() {
    var $preview = $('#jj-palette-inline-preview');
    if (!$preview.length) return;

    var primary = readField('palettes[brand][primary_color]', '#2271B1');
    var primaryHover = readField('palettes[brand][primary_color_hover]', shade(primary, -0.12));
    var secondary = readField('palettes[brand][secondary_color]', '#444444');
    var secondaryHover = readField('palettes[brand][secondary_color_hover]', shade(secondary, -0.12));

    var bg = readField('palettes[system][site_bg]', '#F6F7F7');
    var surface = readField('palettes[system][content_bg]', '#FFFFFF');
    var text = readField('palettes[system][text_color]', '#1D2327');
    var link = readField('palettes[system][link_color]', primary);

    $preview.css('--jj-prev-bg', bg);
    $preview.css('--jj-prev-surface', surface);
    $preview.css('--jj-prev-text', text);
    $preview.css('--jj-prev-link', link);
    $preview.css('--jj-prev-primary', primary);
    $preview.css('--jj-prev-primary-hover', primaryHover);
    $preview.css('--jj-prev-secondary', secondary);
    $preview.css('--jj-prev-secondary-hover', secondaryHover);

    $preview.find('[data-jj-color="primary"]').text(primary);
    $preview.find('[data-jj-color="secondary"]').text(secondary);
    $preview.find('[data-jj-color="bg"]').text(bg);
    $preview.find('[data-jj-color="text"]').text(text);
  }

  $(document).ready(function(){
    var $mount = $('#jj-palette-presets');
    if (!$mount.length) return;

    var BASE_PRESETS = [
      {
        id: 'luxury-black-gold',
        name: 'Black & Gold',
        tags: ['럭셔리', '다크', '고대비', '프리미엄'],
        note: '고급·권위·집중(CTA 강함). 법률/컨설팅/하이엔드에 적합.',
        brand: { primary: '#C9A227', secondary: '#0B0D0F' },
        system: { site_bg: '#0B0D0F', content_bg: '#101418', text_color: '#EAEAEA', link_color: '#C9A227' }
      },
      {
        id: 'trust-navy-cyan',
        name: 'Navy & Cyan',
        tags: ['신뢰', '테크', '차분함'],
        note: '신뢰·안정·정교함. SaaS/데이터/기업 사이트에 적합.',
        brand: { primary: '#1F3C88', secondary: '#00B8D9' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#1D2327', link_color: '#1F3C88' }
      },
      {
        id: 'modern-coral-slate',
        name: 'Coral & Slate',
        tags: ['모던', '따뜻함', '크리에이티브'],
        note: '친근·활기. 브랜딩/포트폴리오/커머스에 적합.',
        brand: { primary: '#FF6B6B', secondary: '#334E68' },
        system: { site_bg: '#FFFFFF', content_bg: '#F7FAFC', text_color: '#102A43', link_color: '#334E68' }
      },
      {
        id: 'minimal-mono',
        name: 'Minimal Mono',
        tags: ['미니멀', '라이트', '가독성'],
        note: '콘텐츠 중심·가독성 강화. 블로그/매거진에 적합.',
        brand: { primary: '#111827', secondary: '#6B7280' },
        system: { site_bg: '#F9FAFB', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#111827' }
      },
      {
        id: 'emerald-charcoal',
        name: 'Emerald & Charcoal',
        tags: ['헬스', '자연', '안정'],
        note: '안정·신선. 헬스/웰니스/교육에 적합.',
        brand: { primary: '#10B981', secondary: '#111827' },
        system: { site_bg: '#F7FAFC', content_bg: '#FFFFFF', text_color: '#111827', link_color: '#10B981' }
      },
      {
        id: 'royal-purple-ice',
        name: 'Royal Purple',
        tags: ['프리미엄', '창의', '브랜딩'],
        note: '독창·세련. 이벤트/크리에이티브 스튜디오에 적합.',
        brand: { primary: '#6D28D9', secondary: '#0EA5E9' },
        system: { site_bg: '#F8FAFC', content_bg: '#FFFFFF', text_color: '#0F172A', link_color: '#6D28D9' }
      },
      {
        id: 'warm-brown-cream',
        name: 'Brown & Cream',
        tags: ['따뜻함', '클래식', '브랜드'],
        note: '따뜻·신뢰. 카페/식품/라이프스타일에 적합.',
        brand: { primary: '#8B5E34', secondary: '#D4A373' },
        system: { site_bg: '#FFF7ED', content_bg: '#FFFFFF', text_color: '#1F2937', link_color: '#8B5E34' }
      },
      {
        id: 'contrast-blue-yellow',
        name: 'Blue & Yellow (CTA)',
        tags: ['고대비', '전환', 'CTA'],
        note: '버튼/링크가 눈에 띄게. 랜딩/광고 페이지에 적합.',
        brand: { primary: '#2563EB', secondary: '#F59E0B' },
        system: { site_bg: '#FFFFFF', content_bg: '#F8FAFC', text_color: '#0F172A', link_color: '#2563EB' }
      }
    ];

    var PRESETS = [];

    function isAiPreset(p) {
      if (!p || typeof p !== 'object') return false;
      if (p.source === 'ai_extension') return true;
      return typeof p.id === 'string' && p.id.indexOf('ai-') === 0;
    }

    function sortAiPresets(a, b) {
      var ap = !!(a && a.pinned);
      var bp = !!(b && b.pinned);
      if (ap !== bp) return ap ? -1 : 1;
      var at = (a && a.created_at) ? String(a.created_at) : '';
      var bt = (b && b.created_at) ? String(b.created_at) : '';
      if (at === bt) return 0;
      return bt < at ? -1 : 1; // desc
    }

    function loadAiPresets() {
      var aiPresets = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets
        : [];
      if (!aiPresets || !aiPresets.length) return [];

      var cleaned = [];
      aiPresets.forEach(function(p){
        if (!p || typeof p !== 'object') return;
        if (!p.id || !p.name || !p.brand || !p.system) return;

        // tags 정규화
        if (!Array.isArray(p.tags)) {
          p.tags = [];
        }
        if (p.tags.indexOf('AI') === -1) {
          p.tags.unshift('AI');
        }

        // note 보정
        if (!p.note) {
          p.note = 'AI로 저장된 팔레트';
        }

        // shape 보정(brand/system)
        p.brand = p.brand || {};
        p.system = p.system || {};
        cleaned.push(p);
      });

      cleaned.sort(sortAiPresets);
      return cleaned;
    }

    function normalizePresetColors(p) {
      p.brand.primary = normalizeHex(p.brand.primary);
      p.brand.secondary = normalizeHex(p.brand.secondary);
      p.brand.primary_hover = normalizeHex(p.brand.primary_hover || shade(p.brand.primary, -0.12));
      p.brand.secondary_hover = normalizeHex(p.brand.secondary_hover || shade(p.brand.secondary, -0.12));
      p.system.site_bg = normalizeHex(p.system.site_bg);
      p.system.content_bg = normalizeHex(p.system.content_bg);
      p.system.text_color = normalizeHex(p.system.text_color);
      p.system.link_color = normalizeHex(p.system.link_color || p.brand.primary);
      return p;
    }

    function rebuildPresets() {
      var ai = loadAiPresets();
      PRESETS = ai.concat(BASE_PRESETS);
      PRESETS.forEach(function(p){
        try { normalizePresetColors(p); } catch (e) {}
      });
    }

    rebuildPresets();

    var selectedId = null;

    function render(filterText) {
      var q = (filterText || '').toLowerCase().trim();
      $mount.empty();

      var $toolbar = $('<div class="jj-preset-toolbar"></div>');
      var $search = $('<input type="search" class="jj-preset-search" placeholder="프리셋 검색 (예: 럭셔리, 신뢰, 다크)"/>');
      $search.val(filterText || '');

      var $includeSystem = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 시스템 팔레트도 함께 적용</label>');
      var $includeComponents = $('<label class="jj-preset-include-system"><input type="checkbox" checked> 버튼/폼/링크 자동 맞추기</label>');

      // AI 프리셋 관리(내보내기/불러오기)
      var aiCount = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
        ? window.jj_admin_params.ai_palette_presets.length
        : 0;
      var $aiTools = $('<div class="jj-preset-ai-tools"></div>');
      if (aiCount > 0) {
        $aiTools.append($('<span class="jj-preset-ai-count"></span>').text('AI 프리셋: ' + aiCount + '개'));
      }
      var $aiExportAll = $('<button type="button" class="button jj-preset-ai-export-all">AI 전체 내보내기</button>');
      var $aiImport = $('<button type="button" class="button jj-preset-ai-import">AI 불러오기</button>');
      var $aiImportInput = $('<input type="file" accept=".json" class="jj-preset-ai-import-input" style="display:none;">');
      $aiTools.append($aiExportAll).append($aiImport).append($aiImportInput);

      $toolbar.append($search).append($includeSystem).append($includeComponents).append($aiTools);
      $mount.append($toolbar);

      var $grid = $('<div class="jj-preset-grid" role="list"></div>');
      var shown = 0;

      PRESETS.forEach(function(p){
        var hay = (p.name + ' ' + p.tags.join(' ') + ' ' + p.note).toLowerCase();
        if (q && hay.indexOf(q) === -1) return;
        shown++;

        var isActive = selectedId === p.id;
        var isAI = isAiPreset(p);
        var isPinned = !!(p && p.pinned);

        var $card = $('<div class="jj-preset-card" role="listitem"></div>');
        $card.toggleClass('is-active', !!isActive);
        $card.toggleClass('is-ai', !!isAI);
        $card.toggleClass('is-pinned', !!isPinned);
        $card.attr('data-preset-id', p.id);

        var swatches = [
          { c: p.brand.primary, label: 'Primary' },
          { c: p.brand.secondary, label: 'Secondary' },
          { c: p.system.site_bg, label: 'BG' },
          { c: p.system.text_color, label: 'Text' }
        ];

        var $sw = $('<div class="jj-preset-swatches" aria-hidden="true"></div>');
        swatches.forEach(function(s){
          var $s = $('<span class="jj-preset-swatch"></span>');
          $s.css('background-color', s.c);
          $s.attr('title', s.label + ': ' + s.c);
          $sw.append($s);
        });

        var $meta = $('<div class="jj-preset-meta"></div>');
        $meta.append($('<div class="jj-preset-title"></div>').text(p.name));
        $meta.append($('<div class="jj-preset-tags"></div>').text(p.tags.join(' · ')));
        $meta.append($('<div class="jj-preset-note"></div>').text(p.note));

        var $main = $('<button type="button" class="jj-preset-card-main"></button>');
        $main.append($sw).append($meta);
        $card.append($main);

        // AI 프리셋: 관리 액션(핀/이름/삭제/복사/내보내기)
        if (isAI) {
          var $cardActions = $('<div class="jj-preset-card-actions" aria-label="AI 프리셋 관리"></div>');

          var $pin = $('<button type="button" class="jj-preset-action jj-preset-ai-pin"><span class="dashicons dashicons-admin-post"></span></button>');
          $pin.attr('title', isPinned ? '상단 고정 해제' : '상단 고정');
          $pin.attr('data-op', isPinned ? 'unpin' : 'pin');

          var $rename = $('<button type="button" class="jj-preset-action jj-preset-ai-rename"><span class="dashicons dashicons-edit"></span></button>');
          $rename.attr('title', '이름 변경');

          var $copy = $('<button type="button" class="jj-preset-action jj-preset-ai-copy"><span class="dashicons dashicons-admin-page"></span></button>');
          $copy.attr('title', '팔레트 JSON 복사');

          var $export = $('<button type="button" class="jj-preset-action jj-preset-ai-export"><span class="dashicons dashicons-download"></span></button>');
          $export.attr('title', '팔레트 JSON 다운로드');

          var $del = $('<button type="button" class="jj-preset-action jj-preset-ai-delete"><span class="dashicons dashicons-trash"></span></button>');
          $del.attr('title', '삭제');

          $cardActions.append($pin, $rename, $copy, $export, $del);
          $card.append($cardActions);
        }

        $grid.append($card);
      });

      if (shown === 0) {
        $grid.append($('<div class="jj-preset-empty"></div>').text('검색 결과가 없습니다.'));
      }

      var $actions = $('<div class="jj-preset-actions"></div>');
      var $apply = $('<button type="button" class="button button-primary jj-preset-apply" disabled>선택한 프리셋 적용</button>');
      var $hint = $('<span class="jj-preset-hint">클릭 → 미리보기 → 적용 (언제든 수정 가능)</span>');
      $actions.append($apply).append($hint);

      $mount.append($grid).append($actions);

      // events
      $search.on('input', function(){ render($(this).val()); });

      $aiExportAll.on('click', function(){
        var list = (window.jj_admin_params && Array.isArray(window.jj_admin_params.ai_palette_presets))
          ? window.jj_admin_params.ai_palette_presets
          : [];
        if (!list.length) {
          alert('내보낼 AI 프리셋이 없습니다.');
          return;
        }
        var payload = {
          exported_at: new Date().toISOString(),
          items: list
        };
        try {
          var data = JSON.stringify(payload, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = 'ai-palette-presets-' + (new Date().toISOString().slice(0,10)) + '.json';
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('내보내기에 실패했습니다.');
        }
      });

      $aiImport.on('click', function(){
        $aiImportInput.trigger('click');
      });

      $aiImportInput.on('change', function(){
        var file = this.files && this.files[0];
        if (!file) return;
        if (!file.name.toLowerCase().endsWith('.json')) {
          alert('JSON 파일만 업로드할 수 있습니다.');
          $(this).val('');
          return;
        }
        var reader = new FileReader();
        reader.onload = function(){
          var txt = String(reader.result || '');
          // Allow wrapped export format {items:[...]} or direct array/object
          var payloadTxt = txt;
          try {
            var parsed = JSON.parse(txt);
            if (parsed && parsed.items && Array.isArray(parsed.items)) {
              payloadTxt = JSON.stringify(parsed.items);
            }
          } catch (e) {
            // keep raw -> server will validate
          }

          if (!confirm('AI 팔레트 프리셋을 불러올까요? (중복 항목은 자동으로 건너뜁니다)')) {
            $(this).val('');
            return;
          }

          ajaxManageAiPreset('import', 'ai-import', null, payloadTxt).then(function(resp){
            if (!resp) return;
            showToast(resp.data.message || '가져오기 완료', 'success');
            render($search.val());
          });
        }.bind(this);
        reader.onerror = function(){
          alert('파일을 읽을 수 없습니다.');
          $(this).val('');
        }.bind(this);
        reader.readAsText(file);
      });

      function showToast(msg, kind) {
        try {
          $('.jj-preset-toast').remove();
          var $t = $('<div class="jj-preset-toast"></div>');
          $t.addClass(kind ? ('is-' + kind) : 'is-info');
          $t.text(msg || '');
          $mount.prepend($t);
          setTimeout(function(){
            $t.fadeOut(200, function(){ $(this).remove(); });
          }, 2600);
        } catch (e) {}
      }

      function ajaxManageAiPreset(op, id, name, payloadJson) {
        var ajaxUrl = (window.jj_admin_params && window.jj_admin_params.ajax_url) ? window.jj_admin_params.ajax_url : (window.ajaxurl || '');
        var security = (window.jj_admin_params && window.jj_admin_params.nonce) ? window.jj_admin_params.nonce : '';
        if (!ajaxUrl || !security) {
          alert('AJAX 설정이 초기화되지 않았습니다.');
          return;
        }

        return $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: {
            action: 'jj_ai_palette_preset_manage',
            security: security,
            op: op,
            id: id,
            name: name || '',
            payload_json: payloadJson || ''
          }
        }).then(function(resp){
          if (!resp || !resp.success) {
            alert((resp && resp.data && resp.data.message) ? resp.data.message : '요청 실패');
            return null;
          }
          // 갱신된 목록을 localize 영역에 반영 후 rebuild
          if (window.jj_admin_params) {
            window.jj_admin_params.ai_palette_presets = resp.data.items || [];
          }
          rebuildPresets();
          return resp;
        });
      }

      function copyToClipboard(text, fallbackLabel) {
        var t = String(text || '');
        if (!t) return;
        try {
          if (navigator && navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            navigator.clipboard.writeText(t).then(function(){
              showToast('클립보드에 복사되었습니다.', 'success');
            }).catch(function(){
              window.prompt(fallbackLabel || '복사하세요:', t);
            });
            return;
          }
        } catch (e) {}
        window.prompt(fallbackLabel || '복사하세요:', t);
      }

      function downloadJson(filename, obj) {
        try {
          var data = JSON.stringify(obj, null, 2);
          var blob = new Blob([data], { type: 'application/json' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url;
          a.download = filename;
          document.body.appendChild(a);
          a.click();
          a.remove();
          setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
        } catch (e) {
          alert('다운로드에 실패했습니다.');
        }
      }

      function findPreset(id) {
        return PRESETS.find(function(x){ return x && x.id === id; }) || null;
      }

      $grid.on('click', '.jj-preset-card-main', function(){
        selectedId = $(this).closest('.jj-preset-card').data('preset-id');
        render($search.val());
      });

      $grid.on('click', '.jj-preset-ai-pin', function(e){
        e.preventDefault(); e.stopPropagation();
        var $c = $(this).closest('.jj-preset-card');
        var id = $c.data('preset-id');
        var op = $(this).attr('data-op') || 'pin';
        ajaxManageAiPreset(op, id).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'info');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-rename', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var next = window.prompt('새 이름을 입력하세요:', preset.name || '');
        if (!next) return;
        ajaxManageAiPreset('rename', id, next).then(function(resp){
          if (!resp) return;
          showToast(resp.data.message || '완료', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-delete', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        if (!confirm('이 AI 프리셋을 삭제할까요?')) return;
        ajaxManageAiPreset('delete', id).then(function(resp){
          if (!resp) return;
          if (selectedId === id) selectedId = null;
          showToast(resp.data.message || '삭제됨', 'success');
          render($search.val());
        });
      });

      $grid.on('click', '.jj-preset-ai-copy', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        copyToClipboard(JSON.stringify(payload, null, 2), '아래 JSON을 복사하세요:');
      });

      $grid.on('click', '.jj-preset-ai-export', function(e){
        e.preventDefault(); e.stopPropagation();
        var id = $(this).closest('.jj-preset-card').data('preset-id');
        var preset = findPreset(id);
        if (!preset) return;
        var payload = {
          id: preset.id,
          name: preset.name,
          tags: preset.tags,
          note: preset.note,
          brand: preset.brand,
          system: preset.system,
          pinned: !!preset.pinned,
          created_at: preset.created_at || ''
        };
        downloadJson('ai-palette-' + id + '.json', payload);
      });

      $apply.prop('disabled', !selectedId);
      $apply.on('click', function(){
        var preset = PRESETS.find(function(x){ return x.id === selectedId; });
        if (!preset) return;

        var includeSystem = $includeSystem.find('input').is(':checked');
        var includeComponents = $includeComponents.find('input').is(':checked');

        // brand
        setField('palettes[brand][primary_color]', preset.brand.primary);
        setField('palettes[brand][primary_color_hover]', preset.brand.primary_hover);
        setField('palettes[brand][secondary_color]', preset.brand.secondary);
        setField('palettes[brand][secondary_color_hover]', preset.brand.secondary_hover);

        // system (옵션)
        if (includeSystem) {
          setField('palettes[system][site_bg]', preset.system.site_bg);
          setField('palettes[system][content_bg]', preset.system.content_bg);
          setField('palettes[system][text_color]', preset.system.text_color);
          setField('palettes[system][link_color]', preset.system.link_color);
        }

        // 미리보기 갱신
        updateInlinePreview();

        // 버튼/폼/링크 자동 매핑 (기존 "일괄 적용" 로직 재사용)
        if (includeComponents) {
          var $btn = $('#jj-style-guide-form').find('.jj-apply-brand-palette-to-components').first();
          if ($btn.length) {
            $btn.trigger('click');
          }
        }
      });
    }

    render('');
    updateInlinePreview();

    // 색상 변경 시 미리보기 자동 갱신
    $('#jj-style-guide-form').on('change input', '.jj-color-field', function(){
      updateInlinePreview();
    });
  });
})(jQuery);


```

```css
/* JJ Style Guide - Palette Presets & Inline Preview */

#jj-style-guide-wrapper .jj-palette-quickstart {
  margin: 18px 0 22px;
  padding: 16px;
  border: 1px solid #c3c4c7;
  border-radius: 8px;
  background: #f8fafc;
}

#jj-style-guide-wrapper .jj-palette-quickstart h3 {
  margin: 0 0 6px;
  font-size: 16px;
}

#jj-style-guide-wrapper .jj-preset-toolbar {
  display: flex;
  gap: 10px;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  margin: 12px 0;
}

#jj-style-guide-wrapper .jj-preset-search {
  flex: 1;
  min-width: 240px;
  max-width: 520px;
  height: 36px;
  padding: 0 10px;
  border-radius: 6px;
}

#jj-style-guide-wrapper .jj-preset-include-system {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #1d2327;
}

#jj-style-guide-wrapper .jj-preset-ai-tools {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: flex-end;
}

#jj-style-guide-wrapper .jj-preset-ai-count {
  font-size: 12px;
  color: #64748b;
  margin-right: 2px;
}

#jj-style-guide-wrapper .jj-preset-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 10px;
}

@media (max-width: 1024px) {
  #jj-style-guide-wrapper .jj-preset-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 640px) {
  #jj-style-guide-wrapper .jj-preset-grid {
    grid-template-columns: 1fr;
  }
}

#jj-style-guide-wrapper .jj-preset-card {
  text-align: left;
  border: 1px solid #c3c4c7;
  background: #fff;
  border-radius: 10px;
  padding: 0;
  cursor: default;
  transition: transform .08s ease, box-shadow .12s ease, border-color .12s ease;
  position: relative;
  overflow: hidden;
}

#jj-style-guide-wrapper .jj-preset-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
  border-color: #8c8f94;
}

#jj-style-guide-wrapper .jj-preset-card.is-active {
  border-color: #ff6400;
  box-shadow: 0 0 0 2px rgba(255,100,0,.15);
}

#jj-style-guide-wrapper .jj-preset-card-main {
  width: 100%;
  display: block;
  text-align: left;
  border: none;
  background: transparent;
  padding: 12px;
  cursor: pointer;
}

#jj-style-guide-wrapper .jj-preset-card-actions {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  gap: 6px;
  align-items: center;
  z-index: 2;
}

#jj-style-guide-wrapper .jj-preset-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 999px;
  border: 1px solid rgba(0,0,0,.12);
  background: rgba(255,255,255,.95);
  cursor: pointer;
  padding: 0;
}

#jj-style-guide-wrapper .jj-preset-action:hover {
  border-color: rgba(0,0,0,.22);
  background: #fff;
}

#jj-style-guide-wrapper .jj-preset-card.is-pinned {
  border-color: #2271b1;
  box-shadow: 0 0 0 2px rgba(34,113,177,.12);
}

#jj-style-guide-wrapper .jj-preset-toast {
  margin: 0 0 10px;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #c3c4c7;
  background: #f6f7f7;
  color: #1d2327;
  font-size: 13px;
}

#jj-style-guide-wrapper .jj-preset-toast.is-success {
  border-color: #b7e1c2;
  background: #edfaef;
  color: #1d6b2f;
}

#jj-style-guide-wrapper .jj-preset-toast.is-info {
  border-color: #b6d1ea;
  background: #f0f6fc;
  color: #135e96;
}

#jj-style-guide-wrapper .jj-preset-swatches {
  display: flex;
  gap: 6px;
  margin-bottom: 10px;
}

#jj-style-guide-wrapper .jj-preset-swatch {
  width: 100%;
  height: 14px;
  border-radius: 999px;
  border: 1px solid rgba(0,0,0,.12);
}

#jj-style-guide-wrapper .jj-preset-meta {
  display: grid;
  gap: 4px;
}

#jj-style-guide-wrapper .jj-preset-title {
  font-weight: 800;
  color: #0f172a;
}

#jj-style-guide-wrapper .jj-preset-tags {
  font-size: 12px;
  color: #475569;
}

#jj-style-guide-wrapper .jj-preset-note {
  font-size: 12px;
  color: #64748b;
  line-height: 1.35;
}

#jj-style-guide-wrapper .jj-preset-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 12px;
}

#jj-style-guide-wrapper .jj-preset-hint {
  font-size: 12px;
  color: #64748b;
}

#jj-style-guide-wrapper .jj-preset-empty {
  grid-column: 1 / -1;
  padding: 18px;
  text-align: center;
  color: #64748b;
  background: rgba(255,255,255,.6);
  border: 1px dashed #c3c4c7;
  border-radius: 10px;
}

/* Inline preview */
#jj-style-guide-wrapper .jj-palette-inline-preview {
  margin-top: 14px;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.12);
  overflow: hidden;
  background: var(--jj-prev-bg, #f6f7f7);
  color: var(--jj-prev-text, #1d2327);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-surface {
  background: var(--jj-prev-surface, #fff);
  margin: 14px;
  border-radius: 12px;
  padding: 16px;
  border: 1px solid rgba(0,0,0,.08);
}

#jj-style-guide-wrapper .jj-palette-inline-preview a {
  color: var(--jj-prev-link, var(--jj-prev-primary, #2271b1));
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-title {
  font-size: 16px;
  font-weight: 900;
  margin: 0 0 6px;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-desc {
  margin: 0 0 12px;
  font-size: 13px;
  line-height: 1.45;
  opacity: .95;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 10px;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn {
  appearance: none;
  border: 1px solid rgba(0,0,0,.12);
  border-radius: 10px;
  padding: 10px 14px;
  font-weight: 800;
  cursor: pointer;
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.primary {
  background: var(--jj-prev-primary, #2271b1);
  border-color: var(--jj-prev-primary, #2271b1);
  color: #fff;
}
#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.primary:hover {
  background: var(--jj-prev-primary-hover, #135e96);
  border-color: var(--jj-prev-primary-hover, #135e96);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.secondary {
  background: var(--jj-prev-secondary, #444);
  border-color: var(--jj-prev-secondary, #444);
  color: #fff;
}
#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-btn.secondary:hover {
  background: var(--jj-prev-secondary-hover, #333);
  border-color: var(--jj-prev-secondary-hover, #333);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-kv {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  font-size: 12px;
  color: rgba(0,0,0,.55);
}

#jj-style-guide-wrapper .jj-palette-inline-preview .jj-prev-kv code {
  background: rgba(0,0,0,.05);
  padding: 2px 6px;
  border-radius: 6px;
}


```

### 위임받은 “순서 고정 + 디테일 강화 + 무오류” 원칙으로, 바로 다음 작업까지 완료했습니다

## 1) WebGPU 스트리밍 UX 세부 고도화(더 안전/더 친절)
- **경과 타이머가 “출력이 없을 때도” 계속 갱신**되도록(0.5s 주기) 개선했습니다.
- 스트리밍 패널에 **툴바**를 추가했습니다:
  - **단계(Stage)**: 대기 / 모델 출력 중 / JSON 파싱 / 완료 / 오류 / 취소
  - **경과/속도/ETA/출력량**: 운영자가 체감할 수 있게 수치로 표시
  - **자동 스크롤 토글**
  - **복사/지우기**
- 출력 폭주로 관리자 화면이 느려지지 않도록 **길이 안전장치**도 유지/강화했습니다.

## 2) 코어 추천 팔레트(AI 저장분) 관리 고도화: “전체 내보내기 + JSON 불러오기(import)”
스타일 센터 > **빠른 시작: 추천 팔레트**에서:
- **AI 전체 내보내기(JSON)**: 현재 AI 프리셋을 한 번에 파일로 저장
- **AI 불러오기(JSON)**: JSON 파일을 업로드해 프리셋을 추가
  - 서버에서 **권한/nonce 검증 + 데이터 구조 검증 + HEX 컬러 정규화 + 중복 시그니처 dedupe + 20개 제한**까지 적용
  - 중복은 자동으로 건너뛰고 “추가/건너뜀” 개수를 메시지로 반환

## 배포 갱신
- `jj_deployment_system.py` 재패키징 완료
- 대시보드: `C:\Users\computer\Desktop\JJ_Distributions_v8.0.0_Master_Control\dashboard.html`
- AI Extension은 버전 **2.0.4** 그대로(이번 라운드는 UX/코어 연동 중심이라 파일명도 유지)이며, 최신 ZIP이 재생성되었습니다.

다음 라운드는 제가 계속 순서 고정으로 제안드리겠습니다. (보통은 **“AI 프리셋 대량 정리(선택 삭제/핀 일괄)” → “프리셋 공유(코드 기반) / 가져오기 UX 고도화”**가 운영 관점에서 가장 효율적입니다.)
