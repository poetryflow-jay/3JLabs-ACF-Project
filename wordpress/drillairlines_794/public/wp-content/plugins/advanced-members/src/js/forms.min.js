var amem;(function(a){// Ensure acf-input.js is available
return'undefined'==typeof acf?void console.error('acf-input.js not found. AMem requires ACF to work.'):void(// Set up all forms on page
amem={forms:{},setup_form:function(a){var b=a.attr('data-key'),c={$el:a,key:b,submissionSteps:[]};// Initialize pages if this is a multi-page form
this.pages.initialize(c),this.ajax.initialize(c),this.forms[b]=c;var d=this;// Intercept the form submission and run ACF validations manually.
// ACF can do this for us but we want control to be able to run our own submission steps after validation.
c.$el.on('submit',function(a){a.preventDefault(),amem.lock(c);// Reset validation status if the form has already been submitted once
// Without this, filter mode won't work as ACF will refuse to validate again
var b=c.$el.data('acf');// Validate form
b&&b.set('status',''),acf.validation.fetch({form:c.$el,failure:function(){amem.unlock(c)},success:function(){// Clone steps to not alter the original array
var a=c.submissionSteps.slice();d.executeSubmissionSteps(c,a)},complete:function(){c.$el.find('.amem-submit-button').prop('disabled',!1)}})}),acf.doAction('amem/form/setup',c)},lock(a){// Disable button to avoid duplicate submissions
a.$el.find('.amem-submit-button').prop('disabled',!0),acf.validation.lockForm(a.$el)},unlock(a){a.$el.find('.amem-submit-button').prop('disabled',!1),acf.validation.unlockForm(a.$el)},addSubmissionStep(a,b,c){// Insert the step at the right position given its priority
for(var d,e={priority:b,fn:c},f=0;f<a.submissionSteps.length;f++)if(d=a.submissionSteps[f],b<d.priority)return void a.submissionSteps.splice(f,0,e);// If we get this far, the step was never inserted and should end up at the end
a.submissionSteps.push(e)},executeSubmissionSteps(a,b){var c=function(){amem.lock(a),a.$el.get(0).submit()};// If there are no steps then we should submit the form immediately
if(0==b.length)return void c();// Get the next step to execute
var d,e=b.shift(),f=this;// Execute next step and pass along callback
d=0==b.length?c:function(){f.executeSubmissionSteps(a,b)},e.fn(d)}},amem.pages={initialize:function(b){var c=this;if($page_fields=b.$el.find('.acf-field-page'),$page_fields.exists()){b.pages=[],b.current_page=0,b.max_page=0,b.show_numbering=!0,b.$page_wrap=a('<div class="amem-page-wrap">'),b.$page_wrap.insertBefore($page_fields.first()),b.$previous_button=$page_fields.first().find('.amem-previous-button'),b.$next_button=$page_fields.first().find('.amem-next-button'),b.show_numbering='true'===$page_fields.first().find('.amem-page-button').attr('data-show-numbering'),b.$previous_button.click(function(a){a.preventDefault(),c.previousPage(b)}),b.$next_button.click(function(a){a.preventDefault(),c.nextPage(b)});var d=b.$el.find('.amem-submit');d.prepend(b.$next_button),d.prepend(b.$previous_button),b.$submit_button=d.find('.amem-submit-button');var e=0;$page_fields.each(function(c,d){var f=a(d),g=e,h=f.nextUntil('.acf-field-page','.acf-field');// If the page contains no fields, we skip it
if(0!=h.length){// Create navigation button
var j=f.find('.amem-page-button').attr('data-index',g);j.click(function(a){a.preventDefault(),amem.pages.navigateToPage(g,b)}),b.show_numbering&&($index=a('<span class="index">').html(g+1),j.prepend($index)),b.$page_wrap.append(j),b.pages.push({$field:f,$fields:h,$button:j}),e++}}),this.refresh(b)}},refresh:function(b){a.each(b.pages,function(c,d){var e=c==b.current_page;// Hide/show fields
d.$button.toggleClass('enabled',c<=b.max_page),d.$button.toggleClass('current',e),d.$fields.each(function(){a(this).toggle(e)})});var c=this.isFirstPage(b),d=this.isLastPage(b);// Refresh next and previous buttons
// Show submit button on last step
b.$previous_button.attr('disabled',!!c||null),b.$next_button.toggle(!d),b.$submit_button.toggle(d)},// Navigate to next page
nextPage:function(a){if(!this.isLastPage(a)){var b=this;this.validatePage(a,a.current_page,function(){b.changePage(a.current_page+1,a)})}},// Navigate to previous page
previousPage:function(a){this.isFirstPage(a)||this.changePage(a.current_page-1,a)},// Navigate to specific page
navigateToPage:function(a,b){if(!(0>a||a>b.max_page)){var c=this;this.validatePage(b,b.current_page,function(){c.changePage(a,b)})}},changePage:function(a,b){var c=b.current_page;b.current_page=a,b.max_page<=b.current_page&&(b.max_page=b.current_page),this.refresh(b),acf.doAction('amem/form/page_changed',a,c,b)},isFirstPage:function(a){return 0==a.current_page},isLastPage:function(a){return a.current_page==a.pages.length-1},validatePage:function(a,b,c){var d=a.pages[b];// Trigger browser validation manually.
// This is normally triggered automatically when a form is submitted.
d.$fields.find('input').each(function(){this.checkValidity()});// Helper function to apply a function on pages except the current one.
var e=function(c){for(i=0;i<a.pages.length;i++)if(i!=b){var d=a.pages[i];c(d)}};// Temporarily remove all other fields outside the current page.
// This way we can use the regular ACF validation on the entire form.
e(function(a){a.$fields.detach()});// Put back the previously removed fields.
var f=function(){e(function(a){a.$fields.insertAfter(a.$field)})};acf.validation.fetch({form:a.$el,lock:!1,reset:!0,success:function(){f(),c()},failure:function(){// We can't use the "complete" callback to put fields back as it's triggered after "success".
f()}})}},amem.ajax={initialize:function(a){var b=this;// Check if form has data-ajax attribute
a.$el.is('[data-ajax]')&&amem.addSubmissionStep(a,100,function(){b.sendSubmission(a)}// Don't call callback. The high priority makes sure AJAX is the last step to run.
// By not calling the callback, the standard form submission won't happen.
)},sendSubmission:function(b){var c=new FormData(b.$el.get(0));// Send AJAX request with action "amem_submission"
c.append('action','amem_submission'),a.ajax({url:acf.get('ajaxurl'),data:c,processData:!1,contentType:!1,type:'post',success:this.onSuccess(b),error:this.onError(b),complete:function(){amem.unlock(b)}})},onSuccess:function(b){return function(c){var d=c.data;switch(acf.doAction('amem/form/ajax/submission',d,b),d.type){case'success_message':// Replace form fields with the success message
var e=a(d.success_message);// remove past messages
b.$el.find('.amem-success-message, .amem-error-message, .amem-updated-message').remove(),b.$el.find('input[name="acf[user_password]"], input[name="acf[user_password_confirm]"], input[name="acf[user_password_current]"], input[name="user_password"], input[name="user_password_confirm"]').val(''),b.$el.find('.pass-strength-result, .pass-strength-result').html(''),b.$el.prepend(e);break;/** @todo Add to form settings and apply, maybe account delete, registration... etc */case'just_success_message':// Replace form fields with the success message
var e=a(d.success_message),f=b.$el.find('.amem-fields');f.replaceWith(e);break;case'redirect':window.location.href=d.redirect_url;}}},onError:function(a){return function(b){var c=a.$el.data('acf'),d=b.responseJSON.data.errors;// Add errors to form
c.addErrors(d),c.showErrors()}}},a(document).ready(function(){a('.amem-form').each(function(){amem.setup_form(a(this))})}))})(jQuery);