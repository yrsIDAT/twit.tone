			function pause(numSeconds)
			// Assumes: numSeconds >= 0
			// Results: pauses execution for numSeconds seconds
			{
			    var now, goalTime;
			
			    now = new Date();
			    goalTime = now.getTime() + 1000*numSeconds;
			
			    while (now.getTime() < goalTime) {
			        now = new Date();
			    }
			}

			function foreach(array) {
				for(var i=0;i<array.length;i++){
					var seperate = array[i];
					carry(seperate);
				}
			}
			
			function carry(array) {
				blip(array);
				//alert(array);
				console.log(array);
			}
			
			var freq;
			var context = new webkitAudioContext();

			function blip(freqq) {
				console.log('Function: blip() started');
				freq = freqq;
				var sinewave = new SineWave(context);
				sinewave.play();
				document.getElementById('hz').innerHTML = freq+"Hz";
				console.log('Function: blip() completed');
				sinewave.pause();
			}
			
			SineWave = function(context, freq) {
				console.log('Function: new SineWave() started');
				var that = this;
				this.x = 0; // Initial sample number
				this.context = context;
				this.node = context.createJavaScriptNode(1024, 1, 1);
				this.node.onaudioprocess = function(e) { that.process(e) };
				console.log('Function: new SineWave() finished');
			}

			SineWave.prototype.process = function(e) {
				console.log('Function: new SineWave.proto.proc started');
				var data = e.outputBuffer.getChannelData(0);
				
				var k = 2* Math.PI * freq / 44100; // 2*PI*frequency*sampleRate
				for (var i = 0;  i < data.length; i++) {
					data[i] = Math.sin(k * this.x++);
				}
				console.log('Function: new SineWave.proto.proc finished');
			}

			SineWave.prototype.play = function() {
				this.node.connect(this.context.destination);
				var now = new Date();
				console.log('Played sound!:'+freq+' '+now.getTime());
			}

			SineWave.prototype.pause = function() {
				pause(1);
				this.node.disconnect();
			}