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
        
        hideInfo('emptythreads');
        hideInfo('oldpost');
        oldPosts.show();
        $('.thread').show();
    }
    
    function hideOldPosts() {
        
        var oldPosts = $('.post:not(.new)');
        var countOld = oldPosts.length;
        
        oldPosts.hide();
        var countEmptyThreads = hideEmptyThreads();
        
        showInfoInPost();
        
        showInfo('emptythreads');
        $('.info .emptythreads .count').html(countEmptyThreads); //should be possible using the functio
        
        $('.post.new .meta').effect('highlight', 1000);
    }   
    
    function hideEmptyThreads() {
        
        var i = 0;
        
        $('.thread').each(function () {
            
            if ($(this).children('.post.new').length == 0) {
                
                $(this).hide();
                i++;
            }
        });
        
        return i;
    }
    
    function showInfoInPost() {
        
        $('.thread').each(function () {
        
            countPost = 0;
            
            $(this).children('.post:not(.new)').each(function () {
                
                countPost++;
            });
            
            console.log(countPost);
            
            if (countPost != 0) {
                
                $(this).find('.info .oldpost').fadeIn().children('.count').html(countPost);
            }
        });
        
        return countPost;
    }
    
});