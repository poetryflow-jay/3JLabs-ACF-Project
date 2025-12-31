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
    const $streamRetry = $('#jj-ai-stream-retry');
    const $streamReparse = $('#jj-ai-stream-reparse');

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

    $streamRetry.on('click', function() {
      if (webgpuState.streaming) return;
      const p = (getSelectedProvider && getSelectedProvider()) ? getSelectedProvider() : '';
      if (p !== 'webgpu') {
        alert('WebGPU 모드에서만 재시도를 지원합니다. (AI 엔진 선택을 WebGPU로 변경하세요)');
        return;
      }
      const prompt = ($('#jj-ai-prompt').val() || '').trim();
      if (!prompt) {
        alert('프롬프트가 비어있습니다.');
        return;
      }
      if (!confirm('같은 프롬프트로 다시 생성할까요?')) return;
      $gen.trigger('click');
    });

    $streamReparse.on('click', function() {
      if (webgpuState.streaming) return;
      const raw = ($stream.length ? ($stream.text() || '') : '');
      if (!raw.trim()) {
        alert('재파싱할 출력이 없습니다.');
        return;
      }
      try {
        setStreamStage('parsing', 'JSON 재파싱 중');
        const out = parseAiJsonResponse(raw);
        renderAiResult(out.settings_patch, out.explanation || '', lastPrompt || '');
        setStreamStage('done', '완료');
        alert('JSON 재파싱에 성공했습니다. (제안 결과를 확인하세요)');
      } catch (e) {
        setStreamStage('error', '오류');
        alert((e && e.message) ? e.message : 'JSON 재파싱 실패');
      }
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
