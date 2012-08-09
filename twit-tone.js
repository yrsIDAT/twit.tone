	var curTime=0
	
	var beepInterval=200
	var beepLast=0
	
	var beepStack=[0, 0]
	
	function beepSound(freq, d) {
    blip(freq, d)
	}
	
	function think(d) {
		curTime=curTime+1
		if (curTime>beepLast+beepInterval && beepStack.length>0) { //Items in array and we have waited for beep
			var curSound=beepStack[0]
			beepSound(curSound, d)
			beepStack.splice(0,1)
			beepLast=curTime
		}
	}
    setInterval(function(){think(parseInt(document.getElementById('duration').value))},1)
			var freq;
			var context = new webkitAudioContext();

			function blip(freqq, delay) {
				freq = freqq;if(!delay){var delay=200;}
				var sinewave = new SineWave(context);
				sinewave.play();
				setTimeout(function() {sinewave.pause()},delay);
				document.getElementById('hz').innerHTML = freq+"Hz";
			}
			
			SineWave = function(context, freq) {
				var that = this;
				this.x = 0; // Initial sample number
				this.context = context;
				this.node = context.createJavaScriptNode(1024, 1, 1);
				this.node.onaudioprocess = function(e) { that.process(e) };
			}

			SineWave.prototype.process = function(e) {
				var data = e.outputBuffer.getChannelData(0);
				
				var k = 2* Math.PI * freq / 44100; // 2*PI*frequency*sampleRate
				for (var i = 0;  i < data.length; i++) {
					data[i] = Math.sin(k * this.x++);
				}
                //status("playing")
			}

			SineWave.prototype.play = function() {
				this.node.connect(this.context.destination);
                //status("play")
			}

			SineWave.prototype.pause = function() {
				this.node.disconnect();
               // status("pause");
			}