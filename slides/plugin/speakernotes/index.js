var express   = require('express');
var fs        = require('fs');
var io        = require('socket.io');
var _         = require('underscore');
var Mustache  = require('mustache');

var app       = express.createServer();
var staticDir = express.static;

io            = io.listen(app);

var opts = {
	port :      8080,
	baseDir :   __dirname + '/../../'
};
console.log("socket setup");

var connectedCount = 0;
var tickerStarted = false;
io.sockets.on('connection', function(socket) {
  console.log("socket connection");
  connectedCount++;
  socket.broadcast.emit("connectedCount", connectedCount);
  socket.on("connect", function() {
    console.log("socket connect");
    connectedCount++;
    socket.broadcast.emit("connectedCount", connectedCount);
  });

  socket.on("disconnect", function() {
    console.log("socket disconnect");
    connectedCount--;
    socket.broadcast.emit("connectedCount", connectedCount);
  });

	socket.on('slidechanged', function(slideData) {
		socket.broadcast.emit('slidedata', slideData);
	});

	socket.on('sendChatMessage', function(chatMessage) {
    console.log("server received sendChatMessage: " + JSON.stringify(chatMessage));
		socket.broadcast.emit('chatmessage', chatMessage);
	});

  var welcomeMessages = [
    "Welcome to Real Time Web Apps with Node.js",
    "These are the participant notes. They will allow you to interact during the presentation",
    "There is live chat below. Join in the conversation!",
    "We hope you enjoy the presentation"
  ];
  var tickerSender;
  var socket;
  var curIndex = 0;
  function sendTickerEvent() {
//    console.log("sendTickerEvent socket", socket);
    var message = welcomeMessages[curIndex];
    console.log("sending ticker event: " + message);
    socket.broadcast.emit("welcomemessage", message);

    var timeout = Math.round(Math.random() * 10000);
    if(timeout < 5000) {
      timeout += 5000;
    }
    curIndex++;
    curIndex = curIndex % welcomeMessages.length;
//    console.log('setting timer for next index: ' + curIndex);
    tickerSender = setTimeout(sendTickerEvent, timeout);
  }
  if (!tickerStarted) {
    sendTickerEvent();
    tickerStarted = true;
  }
});

app.configure(function() {
	[ 'css', 'js', 'plugin', 'lib', 'img' ].forEach(function(dir) {
		app.use('/' + dir, staticDir(opts.baseDir + dir));
	});
  app.use(express.static(__dirname + '/plugin/speakernotes'));
});

app.get("/", function(req, res) {
	fs.createReadStream(opts.baseDir + '/index.html').pipe(res);
});

app.get("/demo", function(req, res) {
  fs.readFile(opts.baseDir + 'plugin/speakernotes/demo.html', function(err, data) {
    res.send(Mustache.to_html(data.toString(), {
      socketId : req.params.socketId
    }));
  });
});

app.get("/notes/:socketId", function(req, res) {

	fs.readFile(opts.baseDir + 'plugin/speakernotes/notes.html', function(err, data) {
		res.send(Mustache.to_html(data.toString(), {
			socketId : req.params.socketId
		}));
	});
	// fs.createReadStream(opts.baseDir + 'speakernotes/notes.html').pipe(res);
});

// Actually listen
app.listen(opts.port || null);

var brown = '\033[33m', 
	green = '\033[32m', 
	reset = '\033[0m';

var slidesLocation = "http://localhost" + ( opts.port ? ( ':' + opts.port ) : '' );

console.log( brown + "reveal.js - Speaker Notes" + reset );
console.log( "1. Open the slides at " + green + slidesLocation + reset );
console.log( "2. Click on the link your JS console to go to the notes page" );
console.log( "3. Advance through your slides and your notes will advance automatically" );

