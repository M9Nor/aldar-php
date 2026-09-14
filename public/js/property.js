
const jumptToSlider = (type) => {
    $('#project_slider .ism-radios li.active').removeClass('active');
    $(`#project_slider .ism-radios li.${type}`)[0].classList.add('active');
    $('#project_slider .ism-radios li.active input').click();
}
$( document ).ready(() => {
    for (let index = 0; index <= $('#project_slider').data('iteration'); index++) {
        $(`#project_slider .ism-radios li.ism-radio-${index}`).addClass($(`#project_slider .data-mini-img-${index}`).data('inputName'));
        $(`#project_slider .ism-radios li.ism-radio-${index} label`).css('background-image', "url(" + $(`#project_slider .data-mini-img-${index}`).data('miniImg') +  ")");
        
    }
})