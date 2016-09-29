$(document).ready(function () {
    
   var simplemde = new SimpleMDE({ 
                                    element: $("#proposal_content")[0],
                                    tabSize: '5',
                                    status: false,
                                    //forceSync: true,
                                    autoDownloadFontAwesome: false,
                                    spellChecker: false,
                                    toolbar:    [{
                                                    name: "bold",
                                                    action: SimpleMDE.toggleBold,
                                                    className: "fa fa-bold",
                                                    title: "Bold"
                                                },
                                                {
                                                    name: "italic",
                                                    action: SimpleMDE.toggleItalic,
                                                    className: "fa fa-italic",
                                                    title: "Italic"
                                                },
                                                {
                                                    name: "strikethrough",
                                                    action: SimpleMDE.toggleStrikethrough,
                                                    className: "fa fa-strikethrough",
                                                    title: "Strikethrough"
                                                },
                                                '|',
                                                {
                                                    name: "code",
                                                    action: SimpleMDE.toggleCodeBlock,
                                                    className: "fa fa-code",
                                                    title: "Code"
                                                },
                                                {
                                                    name: "quote",
                                                    action: SimpleMDE.toggleQuoteBlock,
                                                    className: "fa fa-quote-right",
                                                    title: "Quote"
                                                },
                                                {
                                                    name: "unordered-list",
                                                    action: SimpleMDE.toggleUnorderedList,
                                                    className: "fa fa-list-ul",
                                                    title: "unordered-list"
                                                },
                                                {
                                                    name: "ordered-list",
                                                    action: SimpleMDE.toggleOrderedList,
                                                    className: "fa fa-list-ol",
                                                    title: "ordered-list"
                                                },
                                                '|',
                                                {
                                                    name: "link",
                                                    action: SimpleMDE.drawLink,
                                                    className: "fa fa-link",
                                                    title: "ordered-list"
                                                },
                                                {
                                                    name: "image",
                                                    action: SimpleMDE.drawImage,
                                                    className: "fa fa-picture-o",
                                                    title: "ordered-list"
                                                },
                                                {
                                                    name: "table",
                                                    action: SimpleMDE.drawTable,
                                                    className: "fa fa-table",
                                                    title: "ordered-list"
                                                },
                                                {
                                                    name: "horizontal-rule",
                                                    action: SimpleMDE.drawHorizontalRule,
                                                    className: "fa fa-minus",
                                                    title: "ordered-list"
                                                },
                                                
                                                '|',
                                                {
                                                    name: "custom",
                                                    action: function customFunction(editor){
                                                        toggleWikiInsertArea();
                                                    },
                                                    className: "fa fa-wikipedia-w",
                                                    title: "Custom Button"
                                                },
                                                
                                                {
                                                    name: "custom",
                                                    action: function(editor) { 
                                                        editorFullScreen(editor);
                                                    },
                                                    className: "fa fa-arrows-alt no-disable no-mobile right",
                                                    title: "Fullscreen"
                                                },
                                                {
                                                    name: "preview",
                                                    action: SimpleMDE.togglePreview,
                                                    className: "fa fa-eye no-disable right",
                                                    title: "Preview"
                                                }
                                                
                                            ]
                                });
                 
    //typing in editor
    simplemde.codemirror.on("change", function(){
        
        //changed
        if (simplemde.value() != $('#proposal_content').val()) {
        
            $('#savebutton').addClass('active');
            
        }
        
        //nothing changed
        else {
            
            $('#savebutton').removeClass('active');
        }
    });
    
    //fullscreen
    function editorFullScreen(editor) {
        
        $('.nofullscreen').toggle();
        $('.edit.form').toggleClass('editor-fullscreen');
    }
    
    //insert osm wiki text
    function toggleWikiInsertArea() {
        
        $('#wiki-insert-area').toggle();
    }
    
    //Insert
    $('#wiki-insert-area .insert').click(function () {
        
        var markup = $('#wiki-insert-area textarea').val();
        
        //TODO: route
        
        $.post('http://localhost:8000/de/parse/markup2markdown', { markup : markup }, function (markdown) {
            
            simplemde.value(markdown);
        });
        
        $('#wiki-insert-area textarea').focus();
        
        toggleWikiInsertArea();
    });
    
    //Cancel
    $('#wiki-insert-area .cancel, #wiki-insert-area .dimmer').click(function () {
        
        toggleWikiInsertArea();
    });
              
    //submit form
    $('#savebutton').click(function() {
        
        $('.form form').submit();
    });
    
});