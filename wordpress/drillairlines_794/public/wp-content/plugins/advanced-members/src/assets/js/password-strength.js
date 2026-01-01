function checkPasswordStrength(a, b, c) {
  a = a.val();
  b.removeClass("weak short bad good strong");
  c = wp.passwordStrength.meter(a, c, a);
  b.closest(".amem-input").find("input.password-strength").val(c);
  switch (c) {
    case 2:
      b.addClass("bad").html(pwsL10n.bad);
      break;
    case 3:
      b.addClass("good").html(pwsL10n.good);
      break;
    case 4:
      b.addClass("strong").html(pwsL10n.strong);
      break;
    case 5:
      b.addClass("short").html(pwsL10n.mismatch);
      break;
    case 1:
      b.addClass("short").html(pwsL10n.short);
      break
    default:
      b.addClass("short").html(pwsL10n.unknown);
      break;
  }
  return c;
}
function checkPasswordsMatch(a, b, c) {
  b = b.val();
  a = a.val();
  c.removeClass("weak strong short");
  b == a ? c.addClass("strong").html(acf.__("Passwords Match")) : c.addClass("short").html(pwsL10n.mismatch);
}
jQuery(document).ready(function ($) {
  $("body").on("keyup", ".amem-password-main input", function (b) {
    checkPasswordStrength($(this), $(this).closest(".amem-input").find(".pass-strength-result"), []);
  });
  $("body").on("keyup", ".acf-field-user-password-confirm input", function (b) {
    b = $(this).parents(".acf-field-user-password-confirm").siblings(".amem-password-main").find("input[type=password]");
    checkPasswordsMatch($(this), b, $(this).parents(".acf-input-wrap").siblings(".pass-strength-result"));
  });
});
