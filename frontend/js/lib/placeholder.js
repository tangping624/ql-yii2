(function() {
    var supportPlace = ('placeholder' in document.createElement('input'));
    var placeholderArr = [];

    if (!supportPlace) {
        $('input[placeholder],textarea[placeholder]').each(function() {
            fixPlaceholder($(this));
        });
    }

    function fixPlaceholder(el) {
        if(supportPlace) return;
        var text = el.attr('placeholder');
        placeholderArr.push(text);

        if (el.val() === "") {
            el.val(text).addClass('placeholder');
        }
        el.focus(function() {
            if (this.value === this.getAttribute('placeholder')) {
                this.value = '';
                this.className = this.className.replace('placeholder', '')
            }
        }).blur(function() {
            if (this.value === "") {
                this.value = this.getAttribute('placeholder');
                this.className = this.className + ' placeholder'
            }
        }).closest('form').submit(function() {
            if (this.value === this.getAttribute('placeholder')) {
                this.value = ''
            }
        });
    }

    window.Placeholder = {
        fix: fixPlaceholder,
        arr: placeholderArr
    };
})()
