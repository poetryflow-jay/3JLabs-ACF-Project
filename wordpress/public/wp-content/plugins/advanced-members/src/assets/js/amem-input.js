(function($) {
  // Ensure acf-input.js is available
  if (typeof acf === 'undefined') {
    console.error( 'acf-input.js not found. AMem requires ACF to work.' );
    return;
  }

  let amem_field_types = ['username', 'user_email', 'first_name', 'last_name', 'nickname', 'user_bio', 'user_url', 'user_password', 'user_tos'];
  $.each( amem_field_types, function(i, field_type) {
    acf.registerConditionForFieldType( 'hasValue', field_type );
    acf.registerConditionForFieldType( 'hasNoValue', field_type );
    acf.registerConditionForFieldType( 'equalTo', field_type );
    acf.registerConditionForFieldType( 'notEqualTo', field_type );
    acf.registerConditionForFieldType( 'patternMatch', field_type );
    acf.registerConditionForFieldType( 'contains', field_type );
  } );

  if ( amemL10n.truefalse_types ) {
    let TrueFalseEqualTo = acf.getConditionType('TrueFalseEqualTo');
    let TrueFalseNotEqualTo = acf.getConditionType('TrueFalseNotEqualTo');

    if ( TrueFalseEqualTo && TrueFalseNotEqualTo ) {
      let AMemChecked = TrueFalseEqualTo.extend({
        type: 'AMemChecked',
        fieldTypes: amemL10n.truefalse_types
      });
      acf.registerConditionType(AMemChecked);

      let AMemNotChecked = TrueFalseNotEqualTo.extend({
        type: 'AMemNotChecked',
        fieldTypes: amemL10n.truefalse_types
      });
      acf.registerConditionType(AMemNotChecked);
    }
  }

})(jQuery);