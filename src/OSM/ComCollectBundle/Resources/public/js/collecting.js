$(document).ready(function () {
    
    function collect() {
        
        $('#mailinglist').load('http://localhost:8000/com-collect/mailinglist/collect');
        $('#forum').load('http://localhost:8000/com-collect/forum/collect');
        $('#wikitalk').load('http://localhost:8000/com-collect/wikitalk/collect');
    }
    
    $('#collect').click(function () {
    
        collect();
    });
    
    collect();
});