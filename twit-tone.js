			var freqArray = [587, 740, 880, 1175];    

			var freq;
			var context = new webkitAudioContext();

			function blip() {
				var sinewave = new SineWave(context);
				sinewave.play();
				setInterval(function() {sinewave.pause()},600);
				freq = freqArray[Math.floor(Math.random() * freqArray.length)];
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
			}

			SineWave.prototype.play = function() {
				this.node.connect(this.context.destination);
			}

			SineWave.prototype.pause = function() {
				this.node.disconnect();
			}