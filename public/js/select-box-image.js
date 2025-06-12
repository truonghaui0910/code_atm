jQuery(document).ready(function ($) {
    var langArray = [];
    $('.select-box-image option').each(function () {
        var img = $(this).attr("data-thumbnail");
        var text = this.innerText;
        var value = $(this).val();
        var item = '<li><img src="' + img + '" alt="" value="' + value + '"/><span>' + text + '</span></li>';
        langArray.push(item);
    });

    $('#select-image-ul').html(langArray);

//Set the button value to the first el of the array
    $('.btn-select-image').html(langArray[0]);
    $('.btn-select-image').attr('value', '');

//change button stuff on click
    $('#select-image-ul li').click(function (e) {
        e.preventDefault();
        var img = $(this).find('img').attr("src");
        var value = $(this).find('img').attr('value');
        var text = this.innerText;
        var item = '<li><img src="' + img + '" alt="" /><span>' + text + '</span></li>';
        $('.btn-select-image').html(item);
        $('.btn-select-image').attr('value', value);
        $(".select-image-class").toggle();
        //console.log(value);
    });

    $(".btn-select-image").click(function () {
        $(".select-image-class").toggle();
    });

////check local storage for the lang
//    var sessionLang = localStorage.getItem('lang');
//    if (sessionLang) {
//        //find an item with value of sessionLang
//        var langIndex = langArray.indexOf(sessionLang);
//        $('.btn-select-image').html(langArray[langIndex]);
//        $('.btn-select-image').attr('value', sessionLang);
//    } else {
//        var langIndex = langArray.indexOf('ch');
//        console.log(langIndex);
//        $('.btn-select-image').html(langArray[langIndex]);
//        //$('.btn-select-image').attr('value', 'en');
//    }

});