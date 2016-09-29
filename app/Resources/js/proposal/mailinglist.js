$(document).ready(function () {
    
    function showInfo(key) {
        
        //todo: add infos always on the right side
        
        $('.info .' + key).fadeIn();
    }
    
    function hideInfo(key) {
        
        $('.info .' + key).hide();
    }
    
    //switch new
    $('.options .switchnew').click(function () {
       
        var button = $('.options .switchnew');
       
        if (button.hasClass('active')) {

            showOldPosts();
        }
        
        else {

            hideOldPosts();
        }
        
        button.toggleClass('active');
        return false;
    });
    
    function showOldPosts() {
        
        var oldPosts = $('.post:not(.new)');       
        
        hideInfo('oldpost');
        oldPosts.show();
    }
    
    function hideOldPosts() {
        
        var oldPosts = $('.post:not(.new)');
        var countOld = oldPosts.length;
        
        oldPosts.hide();
        showInfo('oldpost');
        $('.info .oldpost .count').html(countOld); //should be possible using the function
        
        $('.post.new .meta').effect('highlight', 1000);
    }
    
    
    //switch order
    $('.options .switchorder').click(function () {
        
        var button = $('.options .switchorder');
        
        elem = $('.thread');
        
        elem.children().each(function(i, post){
        
            elem.prepend(post);
        });
       
        //echo an info about reordering
        if (!button.hasClass('active')) {

            showInfo('switchorder');
        }
        
        else {

            hideInfo('switchorder');
        }
        
        $(this).toggleClass('active');
    });
    
    
});