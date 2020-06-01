var DEFAULT_TIME_DELAY = 1000;
var $m_xat_logs = $('.m_xat-logs');
var isReceiving = 2;
var URL = '../blocks/xatbot/botman/controller.php';

$('document').ready(function(){
	console.log('foo');
	//----------------------User Sends Message Methods--------------------------------//
	// Method which executes once the enter key on the keyboard is pressed
	// Primary function sends the text which the user typed
	$("textarea").keypress(function(event) {

		// If the enter key is pressed
		if(event.which === 13 ) {
			// Ignore the default function of the enter key(Dont go to a new line)
			event.preventDefault();

			if (isReceiving === 0 && $("#m_xat-inputDiv .m_xat-input")[0].value !== "") {
				// Call the method for sending a message, pass in the text from the user
				send(this.value);

				// reset the size of the text area
				$(".m_xat-input").attr("rows", "1");

				// Clear the text area
				this.value = "";
			}
		}
	});

    $("#m_xat-rec").click(function(event) {

		if ($("#m_xat-inputDiv .m_xat-input")[0].value != "" && isReceiving === 0) {
			send($(".m_xat-input").val());

			$(".m_xat-input").attr("rows", "1");
			$(".m_xat-input").val("");
		}
    });
	
	var boundary = (new Date()).getTime();
	var dataParts = [];
	dataParts.push("--" + boundary,
		'Content-Disposition:form-data; name="driver"',
		'', 'web',
		'--' + boundary,
		'Content-Disposition:form-data; name="message"',
		'', 'hello',
		'--' + boundary + '--');
	$.ajax({
		type: "POST",
		url: URL,
		data: dataParts.join('\r\n'),
		contentType: "multipart/form-data; boundary=" + boundary,
		success: function(data) {
			console.log(data);
			newRecievedMessage(data);
		},
		error: function(error) {
			newRecievedMessage(error);
		}
	});

	setTimeout(function() {
		showLoading();
	}, 200);

});

function send(text) {
	isReceiving = 1;

	// Create a div with the text that the user typed in
	$m_xat_logs.append(
        $('<div/>', {'class': 'm_xat m_xat-self'}).append(
            $('<p/>', {'class': 'm_xat-message', 'text': text})));

	// Find the last message in the chatlogs
	var $sentMessage = $(".m_xat-logs .m_xat").last();

	checkVisibility($sentMessage);
	setTimeout(function() {
		showLoading();
	}, 200);
	console.log(text);
	var boundary = (new Date()).getTime();
	var dataParts = [];
	dataParts.push("--" + boundary,
		'Content-Disposition:form-data; name="driver"',
		'', 'web',
		'--' + boundary,
		'Content-Disposition:form-data; name="message"',
		'', text,
		'--' + boundary + '--');
	$.ajax({
        type: "POST",
		url: URL,
		data: dataParts.join('\r\n'),
		contentType: "multipart/form-data; boundary=" + boundary,
        success: function(data) {
			isReceiving = 2;
			newRecievedMessage(data);
		},
        error: function(error) {
			isReceiving = 2;
			newRecievedMessage(error);
		}
    });
}

function newRecievedMessage(messageText) {

	if (messageText !== '') {
		setTimeout(function () {
			createNewMessage(messageText);
		}, DEFAULT_TIME_DELAY);
	}
}




// Method which takes messages and splits them based off a the delimeter <br 2500>
// The integer in the delimeter is optional and represents the time delay in milliseconds
// if the delimeter is not there then the time delay is set to the default
function multiMessage(message)
{
	// Stores the matches in the message, which match the regex
	var matches;

	// List of message objects, each message will have a text and time delay
	var listOfMessages = [];

	// Regex used to find time delay and text of each message
	var regex = /\<br(?:\s+?(\d+))?\>(.*?)(?=(?:\<br(?:\s+\d+)?\>)|$)/g;

	// While matches are still being found in the message
	while(matches = regex.exec(message))
	{
		// if the time delay is undefined(empty) use the default time delay
		if(matches[1] == undefined)
		{
			matches[1] = DEFAULT_TIME_DELAY;
		}

		// Create an array of the responses which will be buttons
		var messageText  = matches[2].split(/<ar>/);

		// Create a message object and add it to the list of messages
		listOfMessages.push({
				text: messageText[0],
				delay: matches[1]
		});
	}


	// loop index
	var i = 0;

	// Variable for the number of messages
	var numMessages = listOfMessages.length;

	// Show the typing indicator
	showLoading();

	// Function which calls the method createNewMessage after waiting on the message delay
	(function theLoop (listOfMessages, i, numMessages)
	{

		// Method which executes after the timedelay
		setTimeout(function ()
		{

			// Create a new message from the server
			createNewMessage(listOfMessages[i].text);

			// If there are still more messages
			if (i++ < numMessages - 1)
			{
				// Show the typing indicator
				showLoading();

				// Call the method again
				theLoop(listOfMessages, i, numMessages);
			}
		}, listOfMessages[i].delay);

	// Pass the parameters back into the method
	})(listOfMessages, i, numMessages);

}


// Method to create a new div showing the text from API.AI
function createNewMessage(message) {

	// Hide the typing indicator
	hideLoading();

	// take the message and say it back to the user.
	//speechResponse(message);

	// // Show the send button and the text area
	// $('#rec').css('visibility', 'visible');
	// $('textarea').css('visibility', 'visible');

	//jsonParsed = 
	

	// Append a new div to the chatlogs body, with an image and the text from API.AI
	$m_xat_logs.append(
		$('<div/>', {'class': 'm_xat m_xat-bot'}).append(
			//$('<div/>', {'class': 'm_xat-user-photo'}).append($('<img src="../blocks/xatbot/images/ana.jpg" />')),
			$('<p/>', {'class': 'm_xat-message', 'text': message.messages[0].text})));

	// Find the last message in the chatlogs
	var $newMessage = $(".m_xat-logs .m_xat").last();

	// Call the method to see if the message is visible
	checkVisibility($newMessage);
}


// Funtion which shows the typing indicator
// As well as hides the textarea and send button
function showLoading()
{
	$m_xat_logs.append($('#m_xat-loadingGif'));
	$("#m_xat-loadingGif").show();
}



// Function which hides the typing indicator
function hideLoading()
{
	$("#m_xat-loadingGif").hide();

	// reset the size of the text area
	$(".m_xat-input").attr("rows", "1");
}



// Method which checks to see if a message is in visible
function checkVisibility(message)
{
	// Scroll the view down a certain amount
	$m_xat_logs.stop().animate({scrollTop: $m_xat_logs[0].scrollHeight});

	if (isReceiving === 2)
		isReceiving = 0;
}


//----------------------------------------- Resize the textarea ------------------------------------------//
$(document)
    .one('focus.input', 'textarea.input', function(){
        var savedValue = this.value;
        this.value = '';
        this.baseScrollHeight = this.scrollHeight;
        this.value = savedValue;
    })
    .on('input.input', 'textarea.input', function(){
        var minRows = this.getAttribute('data-min-rows')|0, rows;
        this.rows = minRows;
        rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 17);
        this.rows = minRows + rows;
	});
