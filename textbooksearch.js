/**
 * Created by schachenman on 8/15/16.
 */
$(document).ready(function(){
    // On load example

});

$(function(){
    $('#search-btn').on('click',function(){
        if($('#ISBN').val() === ''){
            $('#info-text').append("<div class='alert alert-danger' role='alert'>" +
            "Please enter an ISBN Number!</div>");
            return '';
        }
        var ISBN = $('#ISBN').val();
        // AJAX call to server for searching
        $.ajax({
            url: "https://chaseshak.com/textbooksearch/search.php",
            type: "POST",
            data: {
                ISBN: ISBN
            },
            success: function(response){
                alert("Success: " + response);
                console.log(response);
            },
            error: function(response){
                alert("Error: " + JSON.stringify(response));
            }
        });
    });
});