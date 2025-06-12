function Validator(options) {
    var isFormError = false;
    var notify = false;
    if(options.notify!==undefined){
        notify = options.notify;
    }
    function validate(rule) {
        console.log(rule);
        var input = $(`${options.form} ${rule.input}`);
        var error = rule.test(input.val());
        console.log(error);
        if (error) {
            input.focus();
            input.addClass("invalid");
            if(rule.tab!==undefined){
                $(`[href='${rule.tab}']`).click();
            }
            if(notify){
                $.Notification.autoHideNotify('error', 'top center', 'Notify', error);
            }
            
        } else {
            input.removeClass("invalid");

        }
        return error;
    }
    var form = $(`${options.form}`);
    if (form) {
        options.rules.forEach(function (rule) {
            if (validate(rule)) {
                isFormError = true;
            }
        });
    }
    return isFormError;
}
Validator.isRequired = function (selectors) {
    return {
        input: selectors.input,
        tab: selectors.tab,
        name: selectors.name,
        test: function (value) {
            return (!value || value == -1) ? `${selectors.name} is required` : undefined;
        }
    };
};

