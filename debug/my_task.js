//self.postMessage("worker for eval callbacks");
self.onmessage = function(event) {
  /*self.postMessage*/(eval(event.data));
};

/*

function execWorker(code) {
  this.worker = this.worker ? this.worker : new Worker('http://jean/my_task.js');
  this.worker.onmessage = function(event) {  
    console.log(event.data);
  };
  this.worker.postMessage('('+code.toString()+')()');
}
execWorker(function(){postMessage(location.toString());});

var worker = worker ? worker : new Worker('http://jean/my_task.js');
worker.onmessage = function(event) {  
  console.log("Worker said : " + event.data);
};
worker.('self.postMessage("???");');


var i =(new Date().getTime()) / 1000;
var cont = 0;
while(i+2 != (new Date().getTime()) / 1000) {cont++};
console.log(cont);

worker = worker ? worker : new Worker('http://jean/my_task.js');
worker.onmessage = function(event) {  
  console.log(event.data);
};


worker.postMessage('\
var s = [];\
var o = this;\
if (typeof o != "object") {\
  s = o;\
} \
else for (var i in o) {\
  s.push([i, (o[i] || ["null?", typeof o[i]]).toString()]);\
}\
postMessage(s.toString());');

*/