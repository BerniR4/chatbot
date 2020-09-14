// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// This file is part of XatBotMoodle
//
// XatBotMoodle is a chatbot developed in Catalunya that helps search content in an easy,
// interactive and conversational manner. This project implements a chatbot inside a block
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// XatBotMoodle is a project initiated and leaded by Daniel Amo at the GRETEL research
// group at La Salle Campus Barcelona, Universitat Ramon Llull.
//
// XatBotMoodle is copyrighted 2020 by Daniel Amo and Bernat Rovirosa
// of the La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
// Contact info: Daniel Amo Filvà  danielamo @ gmail.com or daniel.amo @ salle.url.edu.

/**
 * Javascript file to manage the chat interface.
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.block_chatbot = {
    DEFAULT_TIME_DELAY : 1000,
    URL : M.cfg.wwwroot + '/blocks/chatbot/botman/controller.php?context=',
    name : 'block_chatbot',
    isReceiving : 2,

    /**
     * Initiates the connection with BomMan and adds events listeners
     */
    init : function(Y, userId, contextId, courseId) {
        this.userId = userId;
        this.URL = this.URL + contextId + '&course=' + courseId;

        var self = this;
        //Add listeners
        $('textarea.m_xat-input').on('keypress', this.manageKeyPress);
        $('#m_xat-rec').on('click', this.manageSend);

        this.send('welcome_message');

    	setTimeout(function() {
            self.showLoading();
    	}, 200);
    },

    /**
     * Key pressed listener
     */
    manageKeyPress : function(event) {
        var self = M.block_chatbot;
        //Check if key is enter
        if(event.which === 13 ) {
            //Ignore the default function of the enter key (Don't go to a new line)
            event.preventDefault();
            self.manageSend(event);
        }
    },

    /**
     * Comprovations before sending the message
     */
    manageSend : function(event) {
        var self = M.block_chatbot;
        var input = $("#m_xat-inputDiv .m_xat-input")[0];
        //Check if the user is waiting a response and that the text is not empty
        if (self.isReceiving === 0 && input.value !== "") {
            //Call the method for sending a message, pass in the text from the user
            self.isReceiving = 1;

            //Delete the buttons if any
            $('.m_xat-logs>.m_xat.m_xat-self:last-child').remove();
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

    /**
     * Creates the message and sends it
     */
    send : function(message, interactive = false) {
        var self = this;
        var boundary = (new Date()).getTime();
    	var dataParts = [];
    	dataParts.push("--" + boundary,
    		'Content-Disposition:form-data; name="driver"',
            '', 'web',
            '--' + boundary,
    		'Content-Disposition:form-data; name="userId"',
            '', this.userId,
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

    /**
     * Comprovations before printing the message.
     * 
     * @param {Object} message The message received from BotMan
     */
    newRecievedMessage : function(message) {
        var self = this;
        if (message !== '') {
    		setTimeout(function () {
    			self.createNewMessage(message);
    		}, this.DEFAULT_TIME_DELAY);
    	}
    },

    /**
     * Prints the message in the chat.
     * 
     * @param {Object} message The message received from BotMan
     */
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

    /**
     * Prints the buttons in the chat.
     * 
     * @param {Object} actions The actions inside the message received from BotMan
     */
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
        //this.isReceiving = 3;
    },

    /**
     * Manages when a button is clicked.
     * 
     * @param {Event} event 
     */
    buttonClick : function (event) {
        var self = M.block_chatbot;
        $('.m_xat-logs>.m_xat.m_xat-self:last-child').remove();
        $('.m_xat-logs').append(
            $('<div/>', {'class': 'm_xat m_xat-self'}).append(
                $('<p/>', {'class': 'm_xat-message', 'text': event.data.text})
            )
        );
        self.send(event.data.message, true);
    },

    /**
     * Prints an attachment in the chat. It prints the resource name with a link, a separator, 
     * and the course name with a link.
     * 
     * @param {Object} messages The messages received from BotMan
     */
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

    /**
     * Show loading animation while waiting for a response.
     */
    showLoading : function() {
        $('.m_xat-logs').append($('#m_xat-loadingGif'));
    	$("#m_xat-loadingGif").show();
    },

    /**
     * Hide loading animation.
     */
    hideLoading : function() {
    	$('#m_xat-loadingGif').hide();

    	// reset the size of the text area
    	$(".m_xat-input").attr("rows", "1");
    },

    /**
     * Scrolls the chat to display the last message.
     */
    checkVisibility : function() {
        var self = M.block_chatbot;
    	// Scroll the view down a certain amount
    	$('.m_xat-logs').stop().animate({scrollTop: $('.m_xat-logs')[0].scrollHeight});
    	if (this.isReceiving === 2)
    		this.isReceiving = 0;
    }

};
