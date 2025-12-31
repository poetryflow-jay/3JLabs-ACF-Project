/*! jsondiffpatch-lite v0.1.0 | MIT License | (Simple Custom Implementation for JJ Style Guide) */
+(function(root) {
  const diff = (oldObj, newObj) => {
    const changes = {};
    
    // 1. Check for value changes or additions
    for (const key in newObj) {
      if (typeof newObj[key] === 'object' && newObj[key] !== null && !Array.isArray(newObj[key])) {
        // Recursive comparison for objects
        const nestedDiff = diff(oldObj[key] || {}, newObj[key]);
        if (Object.keys(nestedDiff).length > 0) {
          changes[key] = nestedDiff;
        }
      } else if (!oldObj.hasOwnProperty(key) || oldObj[key] !== newObj[key]) {
        // Value changed or added
        changes[key] = [oldObj[key], newObj[key]];
      }
    }

    return changes;
  };

  const formatHtml = (delta, parentKey = '') => {
    let html = '<div class="jj-diff-container">';
    
    for (const key in delta) {
      const val = delta[key];
      const fullKey = parentKey ? `${parentKey}.${key}` : key;

      if (Array.isArray(val) && val.length === 2) {
        // Leaf change: [old, new]
        const oldVal = val[0] === undefined ? '(없음)' : val[0];
        const newVal = val[1];
        
        // Color preview support
        const isColor = (typeof newVal === 'string' && newVal.startsWith('#'));
        const oldSwatch = isColor ? `<span class="jj-diff-swatch" style="background:${oldVal}"></span>` : '';
        const newSwatch = isColor ? `<span class="jj-diff-swatch" style="background:${newVal}"></span>` : '';

        html += `
          <div class="jj-diff-row">
            <span class="jj-diff-key">${fullKey}</span>
            <span class="jj-diff-old">${oldSwatch}${oldVal}</span>
            <span class="jj-diff-arrow">→</span>
            <span class="jj-diff-new">${newSwatch}${newVal}</span>
          </div>
        `;
      } else {
        // Nested object
        html += formatHtml(val, fullKey);
      }
    }
    
    html += '</div>';
    return html;
  };

  root.jjJsonDiff = { diff, formatHtml };
})(window);

