M.block_xatbot = {
    DEFAULT_TIME_DELAY : 1000,
    URL : '../blocks/xatbot/botman/controller.php',
    name : 'block_xatbot',
    isReceiving : 2,

    /**
     * Initiates the connection with botman and adds events listeners
     */
    init : function(Y) {
        var self = this;
        //Add listeners
        $('textarea.m_xat-input').on('keypress', this.manageKeyPress);
        $('#m_xat-rec').on('click', this.manageSend);

        this.send('hello');

    	setTimeout(function() {
            self.showLoading();
    	}, 200);
    },

    manageKeyPress : function(event) {
        var self = M.block_xatbot;
        //Check if key is enter
        if(event.which === 13 ) {
            //Ignore the default function of the enter key (Don't go to a new line)
            event.preventDefault();
            self.manageSend(event);
        }
    },

    manageSend : function(event) {
        var self = M.block_xatbot;
        var input = $("#m_xat-inputDiv .m_xat-input")[0];
        //Check if the user is waiting a response and that the text is not empty
        if (self.isReceiving === 0 && input.value !== "") {
            //Call the method for sending a message, pass in the text from the user
            self.isReceiving = 1;
            // Create a div with the text that the user typed in
            $('.m_xat-logs').append(
                $('<div/>', {'class': 'm_xat m_xat-self'}).append(
                    $('<p/>', {'class': 'm_xat-message', 'text': input.value})));

            // Find the last message in the chatlogs
            var $sentMessage = $(".m_xat-logs .m_xat").last();

            self.checkVisibility($sentMessage);
            setTimeout(function() {
                self.showLoading();
            }, 200);
            self.send(input.value);

            //Reset the size of the text area and clear it
            $(".m_xat-input").attr("rows", "1");
            input.value = "";
        }
    },

    send : function(message, interactive = false) {
        var self = this;
        var boundary = (new Date()).getTime();
    	var dataParts = [];
    	dataParts.push("--" + boundary,
    		'Content-Disposition:form-data; name="driver"',
    		'', 'web',
    		'--' + boundary,
    		'Content-Disposition:form-data; name="message"',
            '', message,
            '--' + boundary,
    		'Content-Disposition:form-data; name="interactive"',
    		'', interactive,
    		'--' + boundary + '--');
    	$.ajax({
            type: "POST",
    		url: self.URL,
    		data: dataParts.join('\r\n'),
    		contentType: "multipart/form-data; boundary=" + boundary,
            success: function(data) {
    			self.isReceiving = 2;
    			self.newRecievedMessage(data);
    		},
            error: function(error) {
    			self.isReceiving = 2;
    			self.newRecievedMessage(error);
    		}
        });
    },

    newRecievedMessage : function(message) {
        var self = this;
        if (message !== '') {
    		setTimeout(function () {
    			self.createNewMessage(message);
    		}, this.DEFAULT_TIME_DELAY);
    	}
    },

    createNewMessage : function(message) {
        //Hide the typing indicator
    	this.hideLoading();

        i = 0;
    	while (i < message.messages.length) {

            if (message.messages[i].attachment != null) {
                this.createAttachment(message.messages.slice(i, i + 4));
                i += 3;
            } else {
                //Append a new div to the chatlogs body
                $('.m_xat-logs').append(
                    $('<div/>', {'class': 'm_xat m_xat-bot'}).append(
                        $('<p/>', {'class': 'm_xat-message', 'text': message.messages[i].text})
                    )
                );
                    
                if (message.messages[i].type === "actions") {
                    this.createButton(message.messages[i].actions);
                }
                i++;
            }
    		
    	}
    	// Find the last message in the chatlogs
    	var newMessage = $(".m_xat-logs .m_xat").last();

    	// Call the method to see if the message is visible
    	this.checkVisibility(newMessage);
    },

    createButton : function(actions) {
        var self = this;
        $('.m_xat-logs').append($('<div/>', {'class': 'm_xat m_xat-self'}).append(
            $('<div/>', {'class': 'm_xat-buttonContainer'})
        ));
        for (i = 0; i < actions.length; i++) {
            $('.m_xat-logs>.m_xat.m_xat-self:last-child>.m_xat-buttonContainer').append(
                $('<div/>', {'class': 'm_xat-button', 'text': actions[i].name})
            );
            $('.m_xat-logs>.m_xat.m_xat-self:last-child .m_xat-button:last-child')
                .on('click', {message: actions[i].value, text: actions[i].name}, self.buttonClick);
        }
        this.isReceiving = 3;
    },

    buttonClick : function (event) {
        var self = M.block_xatbot;
        $('.m_xat-logs>.m_xat.m_xat-self:last-child').remove();
        $('.m_xat-logs').append(
            $('<div/>', {'class': 'm_xat m_xat-self'}).append(
                $('<p/>', {'class': 'm_xat-message', 'text': event.data.text})
            )
        );
        self.send(event.data.message, true);
    },

    createAttachment : function(messages) {
        if (messages[0].attachment.type == "file" && messages[2].attachment.type == "file") {
            $('.m_xat-logs').append(
                $('<div/>', {'class': 'm_xat m_xat-bot'}).append(
                    $('<p/>', {'class': 'm_xat-message'}).append(
                        $('<a/>', {'text': messages[0].text, 'href': messages[0].attachment.url,  
                            'target': '_blank'})
                    ).append(messages[1].text).append(
                        $('<a/>', {'text': messages[2].text, 'href': messages[2].attachment.url,  
                            'target': '_blank'})
                    )
                )
            );
        }
    },

    showLoading : function() {
        $('.m_xat-logs').append($('#m_xat-loadingGif'));
    	$("#m_xat-loadingGif").show();
    },

    hideLoading : function() {
    	$('#m_xat-loadingGif').hide();

    	// reset the size of the text area
    	$(".m_xat-input").attr("rows", "1");
    },

    checkVisibility : function(message) {
        var self = M.block_xatbot;
    	// Scroll the view down a certain amount
    	$('.m_xat-logs').stop().animate({scrollTop: $('.m_xat-logs')[0].scrollHeight});
    	if (this.isReceiving === 2)
    		this.isReceiving = 0;
    }

};
