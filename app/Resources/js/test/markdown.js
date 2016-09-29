$(document).ready(function () {
    
    //shortcut
    $('textarea').bind('keyup', 'f4', function () { parseit(); console.log('shortcut'); });
    
    //buttonclick
    $('input').click(function () {
        
        parseit();
        
    });
    
    //parsen
    function parseit() {
        
        console.log('parsed');
        
        var markup = $('#markup').val();
        
        $.post('http://localhost:8000/de/parse/markup2markdown', { markup : markup }, function (markdown) {
            
            console.log(markdown);
            $('#markdown').val(markdown);
        });
    }
});