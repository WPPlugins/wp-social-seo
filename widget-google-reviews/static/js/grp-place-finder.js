/* Laura Doktorova https://github.com/olado/doT */
(function(){function o(){var a={"&":"&#38;","<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","/":"&#47;"},b=/&(?!#?\w+;)|<|>|"|'|\//g;return function(){return this?this.replace(b,function(c){return a[c]||c}):this}}function p(a,b,c){return(typeof b==="string"?b:b.toString()).replace(a.define||i,function(l,e,f,g){if(e.indexOf("def.")===0)e=e.substring(4);if(!(e in c))if(f===":"){a.defineParams&&g.replace(a.defineParams,function(n,h,d){c[e]={arg:h,text:d}});e in c||(c[e]=g)}else(new Function("def","def['"+
e+"']="+g))(c);return""}).replace(a.use||i,function(l,e){if(a.useParams)e=e.replace(a.useParams,function(g,n,h,d){if(c[h]&&c[h].arg&&d){g=(h+":"+d).replace(/'|\\/g,"_");c.__exp=c.__exp||{};c.__exp[g]=c[h].text.replace(RegExp("(^|[^\\w$])"+c[h].arg+"([^\\w$])","g"),"$1"+d+"$2");return n+"def.__exp['"+g+"']"}});var f=(new Function("def","return "+e))(c);return f?p(a,f,c):f})}function m(a){return a.replace(/\\('|\\)/g,"$1").replace(/[\r\t\n]/g," ")}var j={version:"1.0.1",templateSettings:{evaluate:/\{\{([\s\S]+?(\}?)+)\}\}/g,
interpolate:/\{\{=([\s\S]+?)\}\}/g,encode:/\{\{!([\s\S]+?)\}\}/g,use:/\{\{#([\s\S]+?)\}\}/g,useParams:/(^|[^\w$])def(?:\.|\[[\'\"])([\w$\.]+)(?:[\'\"]\])?\s*\:\s*([\w$\.]+|\"[^\"]+\"|\'[^\']+\'|\{[^\}]+\})/g,define:/\{\{##\s*([\w\.$]+)\s*(\:|=)([\s\S]+?)#\}\}/g,defineParams:/^\s*([\w$]+):([\s\S]+)/,conditional:/\{\{\?(\?)?\s*([\s\S]*?)\s*\}\}/g,iterate:/\{\{~\s*(?:\}\}|([\s\S]+?)\s*\:\s*([\w$]+)\s*(?:\:\s*([\w$]+))?\s*\}\})/g,varname:"it",strip:true,append:true,selfcontained:false},template:undefined,
compile:undefined},q;q=function(){return this||(0,eval)("this")}();q.doT=j;String.prototype.encodeHTML=o();var r={append:{start:"'+(",end:")+'",endencode:"||'').toString().encodeHTML()+'"},split:{start:"';out+=(",end:");out+='",endencode:"||'').toString().encodeHTML();out+='"}},i=/$^/;j.template=function(a,b,c){b=b||j.templateSettings;var l=b.append?r.append:
r.split,e,f=0,g;a=b.use||b.define?p(b,a,c||{}):a;a=("var out='"+(b.strip?a.replace(/(^|\r|\n)\t* +| +\t*(\r|\n|$)/g," ").replace(/\r|\n|\t|\/\*[\s\S]*?\*\//g,""):a).replace(/'|\\/g,"\\$&").replace(b.interpolate||i,function(h,d){return l.start+m(d)+l.end}).replace(b.encode||i,function(h,d){e=true;return l.start+m(d)+l.endencode}).replace(b.conditional||i,function(h,d,k){return d?k?"';}else if("+m(k)+"){out+='":"';}else{out+='":k?"';if("+m(k)+"){out+='":"';}out+='"}).replace(b.iterate||i,function(h,
d,k,s){if(!d)return"';} } out+='";f+=1;g=s||"i"+f;d=m(d);return"';var arr"+f+"="+d+";if(arr"+f+"){var "+k+","+g+"=-1,l"+f+"=arr"+f+".length-1;while("+g+"<l"+f+"){"+k+"=arr"+f+"["+g+"+=1];out+='"}).replace(b.evaluate||i,function(h,d){return"';"+m(d)+"out+='"})+"';return out;").replace(/\n/g,"\\n").replace(/\t/g,"\\t").replace(/\r/g,"\\r").replace(/(\s|;|\}|^|\{)out\+='';/g,"$1").replace(/\+''/g,"").replace(/(\s|;|\}|^|\{)out\+=''\+/g,"$1out+=");if(e&&b.selfcontained)a="String.prototype.encodeHTML=("+
o.toString()+"());"+a;try{return new Function(b.varname,a)}catch(n){typeof console!=="undefined"&&console.log("Could not create a template function: "+a);throw n;}};j.compile=function(a,b){return j.template(a,null,b)}})();

var WPacXDM = WPacXDM || {

    xdm: {},

    channel: {},

    xhr: function(host) {
        if (this.xdm[host] && this.iframe(host)) {
            return this.xdm[host];
        } else {
            return (this.xdm[host] = this.create(host));
        }
    },

    iframe: function(host) {
        return document.getElementById('easyXDM_' + this.channel[host] + '_provider');
    },

    create: function(host) {
        var handler = this;
        this.loadEasyXDM();
        return new easyXDM.Rpc({
            remote: host + '/widget/xdm/index.html',
            onReady: function() {
                var iframe = document.getElementById('easyXDM_' + this.channel + '_provider');
                iframe.setAttribute('style', 'position:absolute!important;top:-2000px!important;left:0!important;');
                handler.channel[host] = this.channel;
            }
        },{
            remote: {
                request: {}
            },
            serializer: {
                stringify: function(obj) {
                    var clone = {
                        id: obj.id,
                        jsonrpc: obj.jsonrpc,
                        method: obj.method,
                        params: obj.params[0]
                    };
                    return handler.stringify(clone);
                },
                parse: function(string) {
                    return JSON.parse(string);
                }
            }
        });
    },

    //TODO: coz if loaded many times occurs error: undefined is not a function
    //TODO: check this behavior on production with loaded widget from 'a' and 'b'
    loadEasyXDM: function() {
        (function(N,d,p,K,k,H){var b=this;var n=Math.floor(Math.random()*10000);var q=Function.prototype;var Q=/^((http.?:)\/\/([^:\/\s]+)(:\d+)*)/;var R=/[\-\w]+\/\.\.\//;var F=/([^:])\/\//g;var I="";var o={};var M=N.easyXDM;var U="easyXDM_";var E;var y=false;var i;var h;function C(X,Z){var Y=typeof X[Z];return Y=="function"||(!!(Y=="object"&&X[Z]))||Y=="unknown"}function u(X,Y){return !!(typeof(X[Y])=="object"&&X[Y])}function r(X){return Object.prototype.toString.call(X)==="[object Array]"}function c(){var Z="Shockwave Flash",ad="application/x-shockwave-flash";if(!t(navigator.plugins)&&typeof navigator.plugins[Z]=="object"){var ab=navigator.plugins[Z].description;if(ab&&!t(navigator.mimeTypes)&&navigator.mimeTypes[ad]&&navigator.mimeTypes[ad].enabledPlugin){i=ab.match(/\d+/g)}}if(!i){var Y;try{Y=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");i=Array.prototype.slice.call(Y.GetVariable("$version").match(/(\d+),(\d+),(\d+),(\d+)/),1);Y=null}catch(ac){}}if(!i){return false}var X=parseInt(i[0],10),aa=parseInt(i[1],10);h=X>9&&aa>0;return true}var v,x;if(C(N,"addEventListener")){v=function(Z,X,Y){Z.addEventListener(X,Y,false)};x=function(Z,X,Y){Z.removeEventListener(X,Y,false)}}else{if(C(N,"attachEvent")){v=function(X,Z,Y){X.attachEvent("on"+Z,Y)};x=function(X,Z,Y){X.detachEvent("on"+Z,Y)}}else{throw new Error("Browser not supported")}}var W=false,J=[],L;if("readyState" in d){L=d.readyState;W=L=="complete"||(~navigator.userAgent.indexOf("AppleWebKit/")&&(L=="loaded"||L=="interactive"))}else{W=!!d.body}function s(){if(W){return}W=true;for(var X=0;X<J.length;X++){J[X]()}J.length=0}if(!W){if(C(N,"addEventListener")){v(d,"DOMContentLoaded",s)}else{v(d,"readystatechange",function(){if(d.readyState=="complete"){s()}});if(d.documentElement.doScroll&&N===top){var g=function(){if(W){return}try{d.documentElement.doScroll("left")}catch(X){K(g,1);return}s()};g()}}v(N,"load",s)}function G(Y,X){if(W){Y.call(X);return}J.push(function(){Y.call(X)})}function m(){var Z=parent;if(I!==""){for(var X=0,Y=I.split(".");X<Y.length;X++){Z=Z[Y[X]]}}return Z.easyXDM}function e(X){N.easyXDM=M;I=X;if(I){U="easyXDM_"+I.replace(".","_")+"_"}return o}function z(X){return X.match(Q)[3]}function f(X){return X.match(Q)[4]||""}function j(Z){var X=Z.toLowerCase().match(Q);var aa=X[2],ab=X[3],Y=X[4]||"";if((aa=="http:"&&Y==":80")||(aa=="https:"&&Y==":443")){Y=""}return aa+"//"+ab+Y}function B(X){X=X.replace(F,"$1/");if(!X.match(/^(http||https):\/\//)){var Y=(X.substring(0,1)==="/")?"":p.pathname;if(Y.substring(Y.length-1)!=="/"){Y=Y.substring(0,Y.lastIndexOf("/")+1)}X=p.protocol+"//"+p.host+Y+X}while(R.test(X)){X=X.replace(R,"")}return X}function P(X,aa){var ac="",Z=X.indexOf("#");if(Z!==-1){ac=X.substring(Z);X=X.substring(0,Z)}var ab=[];for(var Y in aa){if(aa.hasOwnProperty(Y)){ab.push(Y+"="+H(aa[Y]))}}return X+(y?"#":(X.indexOf("?")==-1?"?":"&"))+ab.join("&")+ac}var S=(function(X){X=X.substring(1).split("&");var Z={},aa,Y=X.length;while(Y--){aa=X[Y].split("=");Z[aa[0]]=k(aa[1])}return Z}(/xdm_e=/.test(p.search)?p.search:p.hash));function t(X){return typeof X==="undefined"}var O=function(){var Y={};var Z={a:[1,2,3]},X='{"a":[1,2,3]}';if(typeof JSON!="undefined"&&typeof JSON.stringify==="function"&&JSON.stringify(Z).replace((/\s/g),"")===X){return JSON}if(Object.toJSON){if(Object.toJSON(Z).replace((/\s/g),"")===X){Y.stringify=Object.toJSON}}if(typeof String.prototype.evalJSON==="function"){Z=X.evalJSON();if(Z.a&&Z.a.length===3&&Z.a[2]===3){Y.parse=function(aa){return aa.evalJSON()}}}if(Y.stringify&&Y.parse){O=function(){return Y};return Y}return null};function T(X,Y,Z){var ab;for(var aa in Y){if(Y.hasOwnProperty(aa)){if(aa in X){ab=Y[aa];if(typeof ab==="object"){T(X[aa],ab,Z)}else{if(!Z){X[aa]=Y[aa]}}}else{X[aa]=Y[aa]}}}return X}function a(){var Y=d.body.appendChild(d.createElement("form")),X=Y.appendChild(d.createElement("input"));X.name=U+"TEST"+n;E=X!==Y.elements[X.name];d.body.removeChild(Y)}function A(Y){if(t(E)){a()}var ac;if(E){ac=d.createElement('<iframe name="'+Y.props.name+'"/>')}else{ac=d.createElement("IFRAME");ac.name=Y.props.name}ac.id=ac.name=Y.props.name;delete Y.props.name;if(typeof Y.container=="string"){Y.container=d.getElementById(Y.container)}if(!Y.container){T(ac.style,{position:"absolute",top:"-2000px",left:"0px"});Y.container=d.body}var ab=Y.props.src;Y.props.src="javascript:false";T(ac,Y.props);ac.border=ac.frameBorder=0;ac.allowTransparency=true;Y.container.appendChild(ac);if(Y.onLoad){v(ac,"load",Y.onLoad)}if(Y.usePost){var aa=Y.container.appendChild(d.createElement("form")),X;aa.target=ac.name;aa.action=ab;aa.method="POST";if(typeof(Y.usePost)==="object"){for(var Z in Y.usePost){if(Y.usePost.hasOwnProperty(Z)){if(E){X=d.createElement('<input name="'+Z+'"/>')}else{X=d.createElement("INPUT");X.name=Z}X.value=Y.usePost[Z];aa.appendChild(X)}}}aa.submit();aa.parentNode.removeChild(aa)}else{ac.src=ab}Y.props.src=ab;return ac}function V(aa,Z){if(typeof aa=="string"){aa=[aa]}var Y,X=aa.length;while(X--){Y=aa[X];Y=new RegExp(Y.substr(0,1)=="^"?Y:("^"+Y.replace(/(\*)/g,".$1").replace(/\?/g,".")+"$"));if(Y.test(Z)){return true}}return false}function l(Z){var ae=Z.protocol,Y;Z.isHost=Z.isHost||t(S.xdm_p);y=Z.hash||false;if(!Z.props){Z.props={}}if(!Z.isHost){Z.channel=S.xdm_c.replace(/["'<>\\]/g,"");Z.secret=S.xdm_s;Z.remote=S.xdm_e.replace(/["'<>\\]/g,"");ae=S.xdm_p;if(Z.acl&&!V(Z.acl,Z.remote)){throw new Error("Access denied for "+Z.remote)}}else{Z.remote=B(Z.remote);Z.channel=Z.channel||"default"+n++;Z.secret=Math.random().toString(16).substring(2);if(t(ae)){if(j(p.href)==j(Z.remote)){ae="4"}else{if(C(N,"postMessage")||C(d,"postMessage")){ae="1"}else{if(Z.swf&&C(N,"ActiveXObject")&&c()){ae="6"}else{if(navigator.product==="Gecko"&&"frameElement" in N&&navigator.userAgent.indexOf("WebKit")==-1){ae="5"}else{if(Z.remoteHelper){ae="2"}else{ae="0"}}}}}}}Z.protocol=ae;switch(ae){case"0":T(Z,{interval:100,delay:2000,useResize:true,useParent:false,usePolling:false},true);if(Z.isHost){if(!Z.local){var ac=p.protocol+"//"+p.host,X=d.body.getElementsByTagName("img"),ad;var aa=X.length;while(aa--){ad=X[aa];if(ad.src.substring(0,ac.length)===ac){Z.local=ad.src;break}}if(!Z.local){Z.local=N}}var ab={xdm_c:Z.channel,xdm_p:0};if(Z.local===N){Z.usePolling=true;Z.useParent=true;Z.local=p.protocol+"//"+p.host+p.pathname+p.search;ab.xdm_e=Z.local;ab.xdm_pa=1}else{ab.xdm_e=B(Z.local)}if(Z.container){Z.useResize=false;ab.xdm_po=1}Z.remote=P(Z.remote,ab)}else{T(Z,{channel:S.xdm_c,remote:S.xdm_e,useParent:!t(S.xdm_pa),usePolling:!t(S.xdm_po),useResize:Z.useParent?false:Z.useResize})}Y=[new o.stack.HashTransport(Z),new o.stack.ReliableBehavior({}),new o.stack.QueueBehavior({encode:true,maxLength:4000-Z.remote.length}),new o.stack.VerifyBehavior({initiate:Z.isHost})];break;case"1":Y=[new o.stack.PostMessageTransport(Z)];break;case"2":if(Z.isHost){Z.remoteHelper=B(Z.remoteHelper)}Y=[new o.stack.NameTransport(Z),new o.stack.QueueBehavior(),new o.stack.VerifyBehavior({initiate:Z.isHost})];break;case"3":Y=[new o.stack.NixTransport(Z)];break;case"4":Y=[new o.stack.SameOriginTransport(Z)];break;case"5":Y=[new o.stack.FrameElementTransport(Z)];break;case"6":if(!i){c()}Y=[new o.stack.FlashTransport(Z)];break}Y.push(new o.stack.QueueBehavior({lazy:Z.lazy,remove:true}));return Y}function D(aa){var ab,Z={incoming:function(ad,ac){this.up.incoming(ad,ac)},outgoing:function(ac,ad){this.down.outgoing(ac,ad)},callback:function(ac){this.up.callback(ac)},init:function(){this.down.init()},destroy:function(){this.down.destroy()}};for(var Y=0,X=aa.length;Y<X;Y++){ab=aa[Y];T(ab,Z,true);if(Y!==0){ab.down=aa[Y-1]}if(Y!==X-1){ab.up=aa[Y+1]}}return ab}function w(X){X.up.down=X.down;X.down.up=X.up;X.up=X.down=null}T(o,{version:"2.4.19.3",query:S,stack:{},apply:T,getJSONObject:O,whenReady:G,noConflict:e});o.DomHelper={on:v,un:x,requiresJSON:function(X){if(!u(N,"JSON")){d.write('<script type="text/javascript" src="'+X+'"><\/script>')}}};(function(){var X={};o.Fn={set:function(Y,Z){X[Y]=Z},get:function(Z,Y){if(!X.hasOwnProperty(Z)){return}var aa=X[Z];if(Y){delete X[Z]}return aa}}}());o.Socket=function(Y){var X=D(l(Y).concat([{incoming:function(ab,aa){Y.onMessage(ab,aa)},callback:function(aa){if(Y.onReady){Y.onReady(aa)}}}])),Z=j(Y.remote);this.origin=j(Y.remote);this.destroy=function(){X.destroy()};this.postMessage=function(aa){X.outgoing(aa,Z)};X.init()};o.Rpc=function(Z,Y){if(Y.local){for(var ab in Y.local){if(Y.local.hasOwnProperty(ab)){var aa=Y.local[ab];if(typeof aa==="function"){Y.local[ab]={method:aa}}}}}var X=D(l(Z).concat([new o.stack.RpcBehavior(this,Y),{callback:function(ac){if(Z.onReady){Z.onReady(ac)}}}]));this.origin=j(Z.remote);this.destroy=function(){X.destroy()};X.init()};o.stack.SameOriginTransport=function(Y){var Z,ab,aa,X;return(Z={outgoing:function(ad,ae,ac){aa(ad);if(ac){ac()}},destroy:function(){if(ab){ab.parentNode.removeChild(ab);ab=null}},onDOMReady:function(){X=j(Y.remote);if(Y.isHost){T(Y.props,{src:P(Y.remote,{xdm_e:p.protocol+"//"+p.host+p.pathname,xdm_c:Y.channel,xdm_p:4}),name:U+Y.channel+"_provider"});ab=A(Y);o.Fn.set(Y.channel,function(ac){aa=ac;K(function(){Z.up.callback(true)},0);return function(ad){Z.up.incoming(ad,X)}})}else{aa=m().Fn.get(Y.channel,true)(function(ac){Z.up.incoming(ac,X)});K(function(){Z.up.callback(true)},0)}},init:function(){G(Z.onDOMReady,Z)}})};o.stack.FlashTransport=function(aa){var ac,X,ab,ad,Y,ae;function af(ah,ag){K(function(){ac.up.incoming(ah,ad)},0)}function Z(ah){var ag=aa.swf+"?host="+aa.isHost;var aj="easyXDM_swf_"+Math.floor(Math.random()*10000);o.Fn.set("flash_loaded"+ah.replace(/[\-.]/g,"_"),function(){o.stack.FlashTransport[ah].swf=Y=ae.firstChild;var ak=o.stack.FlashTransport[ah].queue;for(var al=0;al<ak.length;al++){ak[al]()}ak.length=0});if(aa.swfContainer){ae=(typeof aa.swfContainer=="string")?d.getElementById(aa.swfContainer):aa.swfContainer}else{ae=d.createElement("div");T(ae.style,h&&aa.swfNoThrottle?{height:"20px",width:"20px",position:"fixed",right:0,top:0}:{height:"1px",width:"1px",position:"absolute",overflow:"hidden",right:0,top:0});d.body.appendChild(ae)}var ai="callback=flash_loaded"+H(ah.replace(/[\-.]/g,"_"))+"&proto="+b.location.protocol+"&domain="+H(z(b.location.href))+"&port="+H(f(b.location.href))+"&ns="+H(I);ae.innerHTML="<object height='20' width='20' type='application/x-shockwave-flash' id='"+aj+"' data='"+ag+"'><param name='allowScriptAccess' value='always'></param><param name='wmode' value='transparent'><param name='movie' value='"+ag+"'></param><param name='flashvars' value='"+ai+"'></param><embed type='application/x-shockwave-flash' FlashVars='"+ai+"' allowScriptAccess='always' wmode='transparent' src='"+ag+"' height='1' width='1'></embed></object>"}return(ac={outgoing:function(ah,ai,ag){Y.postMessage(aa.channel,ah.toString());if(ag){ag()}},destroy:function(){try{Y.destroyChannel(aa.channel)}catch(ag){}Y=null;if(X){X.parentNode.removeChild(X);X=null}},onDOMReady:function(){ad=aa.remote;o.Fn.set("flash_"+aa.channel+"_init",function(){K(function(){ac.up.callback(true)})});o.Fn.set("flash_"+aa.channel+"_onMessage",af);aa.swf=B(aa.swf);var ah=z(aa.swf);var ag=function(){o.stack.FlashTransport[ah].init=true;Y=o.stack.FlashTransport[ah].swf;Y.createChannel(aa.channel,aa.secret,j(aa.remote),aa.isHost);if(aa.isHost){if(h&&aa.swfNoThrottle){T(aa.props,{position:"fixed",right:0,top:0,height:"20px",width:"20px"})}T(aa.props,{src:P(aa.remote,{xdm_e:j(p.href),xdm_c:aa.channel,xdm_p:6,xdm_s:aa.secret}),name:U+aa.channel+"_provider"});X=A(aa)}};if(o.stack.FlashTransport[ah]&&o.stack.FlashTransport[ah].init){ag()}else{if(!o.stack.FlashTransport[ah]){o.stack.FlashTransport[ah]={queue:[ag]};Z(ah)}else{o.stack.FlashTransport[ah].queue.push(ag)}}},init:function(){G(ac.onDOMReady,ac)}})};o.stack.PostMessageTransport=function(aa){var ac,ad,Y,Z;function X(ae){if(ae.origin){return j(ae.origin)}if(ae.uri){return j(ae.uri)}if(ae.domain){return p.protocol+"//"+ae.domain}throw"Unable to retrieve the origin of the event"}function ab(af){var ae=X(af);if(ae==Z&&af.data.substring(0,aa.channel.length+1)==aa.channel+" "){ac.up.incoming(af.data.substring(aa.channel.length+1),ae)}}return(ac={outgoing:function(af,ag,ae){Y.postMessage(aa.channel+" "+af,ag||Z);if(ae){ae()}},destroy:function(){x(N,"message",ab);if(ad){Y=null;ad.parentNode.removeChild(ad);ad=null}},onDOMReady:function(){Z=j(aa.remote);if(aa.isHost){var ae=function(af){if(af.data==aa.channel+"-ready"){Y=("postMessage" in ad.contentWindow)?ad.contentWindow:ad.contentWindow.document;x(N,"message",ae);v(N,"message",ab);K(function(){ac.up.callback(true)},0)}};v(N,"message",ae);T(aa.props,{src:P(aa.remote,{xdm_e:j(p.href),xdm_c:aa.channel,xdm_p:1}),name:U+aa.channel+"_provider"});ad=A(aa)}else{v(N,"message",ab);Y=("postMessage" in N.parent)?N.parent:N.parent.document;Y.postMessage(aa.channel+"-ready",Z);K(function(){ac.up.callback(true)},0)}},init:function(){G(ac.onDOMReady,ac)}})};o.stack.FrameElementTransport=function(Y){var Z,ab,aa,X;return(Z={outgoing:function(ad,ae,ac){aa.call(this,ad);if(ac){ac()}},destroy:function(){if(ab){ab.parentNode.removeChild(ab);ab=null}},onDOMReady:function(){X=j(Y.remote);if(Y.isHost){T(Y.props,{src:P(Y.remote,{xdm_e:j(p.href),xdm_c:Y.channel,xdm_p:5}),name:U+Y.channel+"_provider"});ab=A(Y);ab.fn=function(ac){delete ab.fn;aa=ac;K(function(){Z.up.callback(true)},0);return function(ad){Z.up.incoming(ad,X)}}}else{if(d.referrer&&j(d.referrer)!=S.xdm_e){N.top.location=S.xdm_e}aa=N.frameElement.fn(function(ac){Z.up.incoming(ac,X)});Z.up.callback(true)}},init:function(){G(Z.onDOMReady,Z)}})};o.stack.NameTransport=function(ab){var ac;var ae,ai,aa,ag,ah,Y,X;function af(al){var ak=ab.remoteHelper+(ae?"#_3":"#_2")+ab.channel;ai.contentWindow.sendMessage(al,ak)}function ad(){if(ae){if(++ag===2||!ae){ac.up.callback(true)}}else{af("ready");ac.up.callback(true)}}function aj(ak){ac.up.incoming(ak,Y)}function Z(){if(ah){K(function(){ah(true)},0)}}return(ac={outgoing:function(al,am,ak){ah=ak;af(al)},destroy:function(){ai.parentNode.removeChild(ai);ai=null;if(ae){aa.parentNode.removeChild(aa);aa=null}},onDOMReady:function(){ae=ab.isHost;ag=0;Y=j(ab.remote);ab.local=B(ab.local);if(ae){o.Fn.set(ab.channel,function(al){if(ae&&al==="ready"){o.Fn.set(ab.channel,aj);ad()}});X=P(ab.remote,{xdm_e:ab.local,xdm_c:ab.channel,xdm_p:2});T(ab.props,{src:X+"#"+ab.channel,name:U+ab.channel+"_provider"});aa=A(ab)}else{ab.remoteHelper=ab.remote;o.Fn.set(ab.channel,aj)}var ak=function(){var al=ai||this;x(al,"load",ak);o.Fn.set(ab.channel+"_load",Z);(function am(){if(typeof al.contentWindow.sendMessage=="function"){ad()}else{K(am,50)}}())};ai=A({props:{src:ab.local+"#_4"+ab.channel},onLoad:ak})},init:function(){G(ac.onDOMReady,ac)}})};o.stack.HashTransport=function(Z){var ac;var ah=this,af,aa,X,ad,am,ab,al;var ag,Y;function ak(ao){if(!al){return}var an=Z.remote+"#"+(am++)+"_"+ao;((af||!ag)?al.contentWindow:al).location=an}function ae(an){ad=an;ac.up.incoming(ad.substring(ad.indexOf("_")+1),Y)}function aj(){if(!ab){return}var an=ab.location.href,ap="",ao=an.indexOf("#");if(ao!=-1){ap=an.substring(ao)}if(ap&&ap!=ad){ae(ap)}}function ai(){aa=setInterval(aj,X)}return(ac={outgoing:function(an,ao){ak(an)},destroy:function(){N.clearInterval(aa);if(af||!ag){al.parentNode.removeChild(al)}al=null},onDOMReady:function(){af=Z.isHost;X=Z.interval;ad="#"+Z.channel;am=0;ag=Z.useParent;Y=j(Z.remote);if(af){T(Z.props,{src:Z.remote,name:U+Z.channel+"_provider"});if(ag){Z.onLoad=function(){ab=N;ai();ac.up.callback(true)}}else{var ap=0,an=Z.delay/50;(function ao(){if(++ap>an){throw new Error("Unable to reference listenerwindow")}try{ab=al.contentWindow.frames[U+Z.channel+"_consumer"]}catch(aq){}if(ab){ai();ac.up.callback(true)}else{K(ao,50)}}())}al=A(Z)}else{ab=N;ai();if(ag){al=parent;ac.up.callback(true)}else{T(Z,{props:{src:Z.remote+"#"+Z.channel+new Date(),name:U+Z.channel+"_consumer"},onLoad:function(){ac.up.callback(true)}});al=A(Z)}}},init:function(){G(ac.onDOMReady,ac)}})};o.stack.ReliableBehavior=function(Y){var aa,ac;var ab=0,X=0,Z="";return(aa={incoming:function(af,ad){var ae=af.indexOf("_"),ag=af.substring(0,ae).split(",");af=af.substring(ae+1);if(ag[0]==ab){Z="";if(ac){ac(true)}}if(af.length>0){aa.down.outgoing(ag[1]+","+ab+"_"+Z,ad);if(X!=ag[1]){X=ag[1];aa.up.incoming(af,ad)}}},outgoing:function(af,ad,ae){Z=af;ac=ae;aa.down.outgoing(X+","+(++ab)+"_"+af,ad)}})};o.stack.QueueBehavior=function(Z){var ac,ad=[],ag=true,aa="",af,X=0,Y=false,ab=false;function ae(){if(Z.remove&&ad.length===0){w(ac);return}if(ag||ad.length===0||af){return}ag=true;var ah=ad.shift();ac.down.outgoing(ah.data,ah.origin,function(ai){ag=false;if(ah.callback){K(function(){ah.callback(ai)},0)}ae()})}return(ac={init:function(){if(t(Z)){Z={}}if(Z.maxLength){X=Z.maxLength;ab=true}if(Z.lazy){Y=true}else{ac.down.init()}},callback:function(ai){ag=false;var ah=ac.up;ae();ah.callback(ai)},incoming:function(ak,ai){if(ab){var aj=ak.indexOf("_"),ah=parseInt(ak.substring(0,aj),10);aa+=ak.substring(aj+1);if(ah===0){if(Z.encode){aa=k(aa)}ac.up.incoming(aa,ai);aa=""}}else{ac.up.incoming(ak,ai)}},outgoing:function(al,ai,ak){if(Z.encode){al=H(al)}var ah=[],aj;if(ab){while(al.length!==0){aj=al.substring(0,X);al=al.substring(aj.length);ah.push(aj)}while((aj=ah.shift())){ad.push({data:ah.length+"_"+aj,origin:ai,callback:ah.length===0?ak:null})}}else{ad.push({data:al,origin:ai,callback:ak})}if(Y){ac.down.init()}else{ae()}},destroy:function(){af=true;ac.down.destroy()}})};o.stack.VerifyBehavior=function(ab){var ac,aa,Y,Z=false;function X(){aa=Math.random().toString(16).substring(2);ac.down.outgoing(aa)}return(ac={incoming:function(af,ad){var ae=af.indexOf("_");if(ae===-1){if(af===aa){ac.up.callback(true)}else{if(!Y){Y=af;if(!ab.initiate){X()}ac.down.outgoing(af)}}}else{if(af.substring(0,ae)===Y){ac.up.incoming(af.substring(ae+1),ad)}}},outgoing:function(af,ad,ae){ac.down.outgoing(aa+"_"+af,ad,ae)},callback:function(ad){if(ab.initiate){X()}}})};o.stack.RpcBehavior=function(ad,Y){var aa,af=Y.serializer||O();var ae=0,ac={};function X(ag){ag.jsonrpc="2.0";aa.down.outgoing(af.stringify(ag))}function ab(ag,ai){var ah=Array.prototype.slice;return function(){var aj=arguments.length,al,ak={method:ai};if(aj>0&&typeof arguments[aj-1]==="function"){if(aj>1&&typeof arguments[aj-2]==="function"){al={success:arguments[aj-2],error:arguments[aj-1]};ak.params=ah.call(arguments,0,aj-2)}else{al={success:arguments[aj-1]};ak.params=ah.call(arguments,0,aj-1)}ac[""+(++ae)]=al;ak.id=ae}else{ak.params=ah.call(arguments,0)}if(ag.namedParams&&ak.params.length===1){ak.params=ak.params[0]}X(ak)}}function Z(an,am,ai,al){if(!ai){if(am){X({id:am,error:{code:-32601,message:"Procedure not found."}})}return}var ak,ah;if(am){ak=function(ao){ak=q;X({id:am,result:ao})};ah=function(ao,ap){ah=q;var aq={id:am,error:{code:-32099,message:ao}};if(ap){aq.error.data=ap}X(aq)}}else{ak=ah=q}if(!r(al)){al=[al]}try{var ag=ai.method.apply(ai.scope,al.concat([ak,ah]));if(!t(ag)){ak(ag)}}catch(aj){ah(aj.message)}}return(aa={incoming:function(ah,ag){var ai=af.parse(ah);if(ai.method){if(Y.handle){Y.handle(ai,X)}else{Z(ai.method,ai.id,Y.local[ai.method],ai.params)}}else{var aj=ac[ai.id];if(ai.error){if(aj.error){aj.error(ai.error)}}else{if(aj.success){aj.success(ai.result)}}delete ac[ai.id]}},init:function(){if(Y.remote){for(var ag in Y.remote){if(Y.remote.hasOwnProperty(ag)){ad[ag]=ab(Y.remote[ag],ag)}}}aa.down.init()},destroy:function(){for(var ag in Y.remote){if(Y.remote.hasOwnProperty(ag)&&ad.hasOwnProperty(ag)){delete ad[ag]}}aa.down.destroy()}})};b.easyXDM=o})(window,document,location,window.setTimeout,decodeURIComponent,encodeURIComponent);
    },

    get: function(xhrhost, url, data, success, complete) {
        this.send(xhrhost, url, 'GET', data, success, complete);
    },

    post: function(xhrhost, url, data, success, complete) {
        this.send(xhrhost, url, 'POST', data, success, complete);
    },

    send: function(xhrhost, url, type, data, success, complete) {
        if (data) {
            for (d in data) {
                if (data.hasOwnProperty(d)) {
                    var val = data[d];
                    if (typeof val == 'string') {
                        data[d] = this.escape(val);
                    } else if (typeof val == 'undefined') {
                        delete data[d];
                    }
                }
            }
        }
        this.xhr(xhrhost).request({url: url, method: type, headers: {'Accept': 'application/json;'}, data: data},
            function(res) {
                if (success) {
                    if (res.data) {
                        var json;
                        try { json = JSON.parse(res.data); } catch (e) {}
                        success(json || res.data);
                    } else {
                        success();
                    }
                }
                if (complete) complete();
            }, function(res) {
                if (complete) complete();
            }
        );
    },

    escape: function(str) {
        var escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
            meta = {'\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f', '\r': '\\r', '"' : '\\"', '\\': '\\\\'};

        escapable.lastIndex = 0;
        return escapable.test(str) ?
            str.replace(escapable, function (a) {
                var c = meta[a];
                return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            }) : str;
    },

    stringify: function (obj) {
        var t = typeof (obj);
        if (t != "object" || obj === null) {
            if (t == "string"){obj = '"'+obj+'"';}
            return String(obj);
        }
        else {
            var n, v, json = [], arr = (obj && obj.constructor == Array);
            for (n in obj) {
                if (obj.hasOwnProperty(n)) {
                    v = obj[n]; t = typeof(v);
                    if (t == "string"){v = '"'+v+'"';}else if (t == "object" && v !== null){v = this.stringify(v);}
                    json.push((arr ? "" : '"' + n + '":') + String(v));
                }
            }
            return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
        }
    }
};
"object" !== typeof JSON && (JSON = {});
(function() {
    function a(a) {
        return 10 > a ? "0" + a : a
    }

    function b(a) {
        e.lastIndex = 0;
        return e.test(a) ? '"' + a.replace(e, function(a) {
            var b = h[a];
            return "string" === typeof b ? b : "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
        }) + '"' : '"' + a + '"'
    }

    function c(a, d) {
        var e, h, m, q, r = f,
            l, k = d[a];
        k && ("object" === typeof k && "function" === typeof k.toJSON) && (k = k.toJSON(a));
        "function" === typeof n && (k = n.call(d, a, k));
        switch (typeof k) {
            case "string":
                return b(k);
            case "number":
                return isFinite(k) ? String(k) : "null";
            case "boolean":
            case "null":
                return String(k);
            case "object":
                if (!k) return "null";
                f += g;
                l = [];
                if ("[object Array]" === Object.prototype.toString.apply(k)) {
                    q = k.length;
                    for (e = 0; e < q; e += 1) l[e] = c(e, k) || "null";
                    m = 0 === l.length ? "[]" : f ? "[\n" + f + l.join(",\n" + f) + "\n" + r + "]" : "[" + l.join(",") + "]";
                    f = r;
                    return m
                }
                if (n && "object" === typeof n)
                    for (q = n.length, e = 0; e < q; e += 1) "string" === typeof n[e] && (h = n[e], (m = c(h, k)) && l.push(b(h) + (f ? ": " : ":") + m));
                else
                    for (h in k) Object.prototype.hasOwnProperty.call(k, h) && (m = c(h, k)) && l.push(b(h) + (f ? ": " : ":") + m);
                m = 0 === l.length ? "{}" : f ? "{\n" + f + l.join(",\n" +
                    f) + "\n" + r + "}" : "{" + l.join(",") + "}";
                f = r;
                return m
        }
    }
    "function" !== typeof Date.prototype.toJSON && (Date.prototype.toJSON = function() {
        return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + a(this.getUTCMonth() + 1) + "-" + a(this.getUTCDate()) + "T" + a(this.getUTCHours()) + ":" + a(this.getUTCMinutes()) + ":" + a(this.getUTCSeconds()) + "Z" : null
    }, String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function() {
        return this.valueOf()
    });
    var d, e, f, g, h, n;
    "function" !== typeof JSON.stringify && (e = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        h = {
            "\b": "\\b",
            "\t": "\\t",
            "\n": "\\n",
            "\f": "\\f",
            "\r": "\\r",
            '"': '\\"',
            "\\": "\\\\"
        }, JSON.stringify = function(a, b, d) {
            var e;
            g = f = "";
            if ("number" === typeof d)
                for (e = 0; e < d; e += 1) g += " ";
            else "string" === typeof d && (g = d);
            if ((n = b) && "function" !== typeof b && ("object" !== typeof b || "number" !== typeof b.length)) throw Error("JSON.stringify");
            return c("", {
                "": a
            })
        });
    "function" !== typeof JSON.parse && (d = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, JSON.parse = function(a,
        b) {
        function c(a, d) {
            var e, f, g = a[d];
            if (g && "object" === typeof g)
                for (e in g) Object.prototype.hasOwnProperty.call(g, e) && (f = c(g, e), void 0 !== f ? g[e] = f : delete g[e]);
            return b.call(a, d, g)
        }
        var e;
        a = String(a);
        d.lastIndex = 0;
        d.test(a) && (a = a.replace(d, function(a) {
            return "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
        }));
        if (/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) return e =
            eval("(" + a + ")"), "function" === typeof b ? c({
                "": e
            }, "") : e;
        throw new SyntaxError("JSON.parse");
    })
})();
var WPacFastjs = WPacFastjs || {
    emailRegex: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    get: function(a, b) {
        return document.querySelector(a + " " + b)
    },
    extend: function(a, b) {
        var c = {},
            d;
        for (d in a) Object.prototype.hasOwnProperty.call(a, d) && (c[d] = a[d]);
        for (d in b) Object.prototype.hasOwnProperty.call(b, d) && (c[d] = b[d]);
        return c
    },
    create: function(a, b, c, d) {
        a = document.createElement(a);
        b && this.addcl(a, b);
        c &&
            a.setAttribute("title", c);
        d && a.setAttribute("style", d);
        return a
    },
    addcls: function(a, b) {
        for (var c = 0; c < b.length; c++) {
            var d = b[c];
            0 > a.className.indexOf(d) && (a.className += " " + d)
        }
    },
    addcl: function(a, b) {
        a && 0 > a.className.indexOf(b) && (a.className += " " + b, a.className = a.className.trim())
    },
    remcl: function(a, b) {
        a && (a.className = a.className.replace(b, " "))
    },
    hascl: function(a, b) {
        return a && -1 < a.className.indexOf(b)
    },
    on: function(a, b, c) {
        if (a) {
            var d = this;
            a.addEventListener ? a.addEventListener(b, function(b) {
                !1 == c.call(a, b) &&
                    d.stop(b)
            }, !1) : a.attachEvent("on" + b, function(b) {
                b.stopPropagation = b.stopPropagation || function() {
                    this.cancelBubble = !0
                };
                b.preventDefault = b.preventDefault || function() {
                    this.returnValue = !1
                };
                !1 == c.call(a, b) && d.stop(b)
            })
        }
    },
    on2: function(a, b, c, d) {
        if (a && (a = a.querySelector(b))) this.on(a, c, d)
    },
    onall: function(a, b, c) {
        for (var d = 0; d < a.length; d++) this.on(a[d], b, c)
    },
    onall2: function(a, b, c, d) {
        this.onall(a.querySelectorAll(b), c, d)
    },
    stop: function(a) {
        a.preventDefault();
        a.stopPropagation()
    },
    parents: function(a, b) {
        var c = a.parentNode,
            d = !1;
        this.each(c.className.split(" "), function(a) {
            !d && (d = a == b)
        });
        return d ? c : this.parents(c, b)
    },
    parentsel: function(a, b) {
        var c = a.parentNode;
        return 0 > c.tagName.toLowerCase().indexOf(b) ? this.parentsel(c, b) : c
    },
    show: function(a, b) {
        var c = b.querySelector(a);
        this.show2(c)
    },
    show2: function(a) {
        a && (a.style.display = "")
    },
    hide: function(a, b) {
        var c = b.querySelector(a);
        this.hide2(c)
    },
    hide2: function(a) {
        a && (a.style.display = "none")
    },
    html: function(a, b) {
        a && (a.innerHTML = "", this.isString(b) ? a.innerHTML = b : a.appendChild(b))
    },
    prepend: function(a, b) {
        a.insertBefore(b, a.firstChild)
    },
    rm: function(a) {
        a && a.parentNode && a.parentNode.removeChild(a)
    },
    rm2: function(a, b) {
        var c = a.querySelector(b);
        this.rm(c)
    },
    each: function(a, b) {
        if ("undefined" == typeof a.length) b(a, 0);
        else
            for (var c = 0; c < a.length; c++) b(a[c], c)
    },
    css: function(a, b, c) {
        this.isInteger(c) && (c += "px");
        a.style[b] = c
    },
    child: function(a, b) {
        for (var c = a.children.length; c--;) {
            var d = a.children[c];
            if (8 != d.nodeType && -1 < d.className.indexOf(b)) return d
        }
    },
    children: function(a) {
        for (var b = [], c = a.children.length; c--;) 8 !=
            a.children[c].nodeType && b.unshift(a.children[c]);
        return b
    },
    icss: function(a, b) {
        return a + ":" + b + "px!important;"
    },
    transCss: function(a, b) {
        var c = "overflow-y:hidden!important;-webkit-transition:" + b + " .5s ease-in-out!important;-moz-transition:" + b + " .5s ease-in-out!important;-o-transition:" + b + " .5s ease-in-out!important;transition:" + b + " .5s ease-in-out!important;";
        a.setAttribute("style", c);
        return c
    },
    prependSlide: function(a, b) {
        var c = this,
            d = this.transCss(a, "max-height");
        b.insertBefore(a, b.firstChild);
        var e =
            a.offsetHeight;
        a.setAttribute("style", this.icss("max-height", 0) + d);
        setTimeout(function() {
            a.setAttribute("style", c.icss("max-height", e) + d);
            setTimeout(function() {
                a.setAttribute("style", "")
            }, 1E3)
        }, 1)
    },
    slidedwn: function(a) {
        a.style.display = "";
        var b = a.offsetHeight;
        a.setAttribute("style", this.transCss(a, "height"));
        a.style.height = "0";
        setTimeout(function() {
            a.style.height = b + "px";
            setTimeout(function() {
                a.setAttribute("style", "")
            }, 500)
        }, 5)
    },
    slideup: function(a, b) {
        a.setAttribute("style", this.transCss(a, "height"));
        a.style.height = a.offsetHeight + "px";
        setTimeout(function() {
            a.style.height = "0";
            setTimeout(function() {
                a.setAttribute("style", "display:none");
                b && b()
            }, 500)
        }, 5)
    },
    title: function() {
        var a = document.getElementsByTagName("title")[0];
        return a && a.textContent || ""
    },
    nextES: function(a) {
        do a = a.nextSibling; while (a && 1 !== a.nodeType);
        return a
    },
    next: function(a) {
        return a.nextElementSibling || this.nextES(a)
    },
    prevES: function(a) {
        do a = a.previousSibling; while (a && 1 !== a.nodeType);
        return a
    },
    prev: function(a) {
        return a.previousElementSibling ||
            this.prevES(a)
    },
    after: function(a, b) {
        a.parentNode.insertBefore(b, a.nextSibling)
    },
    before: function(a, b) {
        a.parentNode.insertBefore(b, a)
    },
    isVisible: function(a) {
        return 0 < a.offsetWidth && 0 < a.offsetHeight
    },
    isInteger: function(a) {
        return a && 0 === a % 1
    },
    isString: function(a) {
        return "string" == typeof a
    },
    afun: function(a) {
        var b = "wpac_" + Math.floor(1000001 * Math.random());
        window[b] = function(c) {
            window[b] = void 0;
            try {
                delete window[b]
            } catch (d) {}
            a(c)
        };
        return b
    },
    params: function(a, b, c) {
        var d = [];
        if (b)
            for (p in b) d.push(encodeURIComponent(p) +
                "=" + encodeURIComponent(b[p]));
        c && d.push("callback=" + this.afun(c));
        return 0 < d.length ? (b = 0 > a.indexOf("?") ? "?" : "&", a + (b + d.join("&"))) : a
    },
    jsonp: function(a, b, c) {
        var d = document.createElement("script");
        d.src = this.params(a, b, c);
        d.type = "text/javascript";
        (document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(d)
    },
    popup: function(a, b, c, d, e, f) {
        e = e || screen.height / 2 - c / 2;
        f = f || screen.width / 2 - b / 2;
        return window.open(this.params(a, d), "", "location=1,status=1,resizable=yes,width=" +
            b + ",height=" + c + ",top=" + e + ",left=" + f)
    },
    inArray: function(a, b) {
        for (var c = 0; c < a.length; c++)
            if (a[c] === b) return c;
        return -1
    },
    txt: function(a, b) {
        "textContent" in a ? a.textContent = b : a.innerText = b
    },
    cbs: function(a, b, c, d) {
        if (a.callback && (a = a.callback[b]) && 0 < a.length)
            for (b = 0; b < a.length; b++)
                if (d) a[b].call(d, c);
                else a[b](c)
    },
    extendcbs: function(a, b) {
        a.callback = a.callback || {};
        for (cb in b) Object.prototype.hasOwnProperty.call(b, cb) && (Object.prototype.hasOwnProperty.call(a.callback, cb) || (a.callback[cb] = []), a.callback[cb].push(b[cb]));
        return a.callback
    },
    isemail: function(a) {
        return this.emailRegex.test(a)
    },
    getParam: function(a) {
        if (location.search && -1 < location.search.indexOf(a)) {
            if (!this.urlparams) {
                this.urlParams = {};
                var b = this;
                location.search.substr(1).split("&").forEach(function(a) {
                    a = a.split("=");
                    b.urlParams[a[0]] = decodeURIComponent(a[1])
                })
            }
            return this.urlParams[a]
        }
    },
    urlsToHyperlinks: function(a) {
        return a.replace(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig, '<a href="$1" target="_blank">$1</a>').replace(/(^|[^\/])(www\.[\S]+(\b|$))/ig,
            '$1<a href="http://$2" target="_blank" rel="nofollow">$2</a>')
    },
    escapeHtml: function(a) {
        return a ? document.createElement("div").appendChild(document.createTextNode(a)).parentNode.innerHTML : ""
    },
    escapeHtmlWithLinks: function(a) {
        return this.urlsToHyperlinks(this.escapeHtml(a))
    }
};
String.prototype.trim || (String.prototype.trim = String.prototype.trim || function() {
    return this.replace(/^\s+|\s+$/g, "")
});
String.prototype.capitalize || (String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1)
});
var WPacSVGIcon = WPacSVGIcon || function() {
    var a = function(a, b, c, g, h) {
            return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="' + a + '" height="' + b + '" viewBox="' + (h || "0 0 1792 1792") + '"><path d="' + g + '"' + (c ? ' fill="' + c + '"' : "") + "/></svg>"
        },
        b = function(a, b, c, g, h) {
            return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="' + a + '" height="' + b + '" viewBox="' + c + '"><g transform="translate(' + g + ') scale(0.05,-0.05)"><path fill="#fff" d="' + h + '"></path></g></svg>'
        },
        c = {
            star_o: "M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z",
            star_half: "M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z",
            star: "M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"
        };
    return {
        path: c,
        star_o: a(22, 22, "#ccc", c.star_o),
        star_half: a(22, 22, "#ff9800", c.star_half),
        star: a(22, 22, "#ff9800", c.star),
        pencil: a(14, 14, "#666", "M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"),
        caret: a(14, 14, "#666", "M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"),
        check: a(14, 14, "#666", "M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"),
        reply: a(14, 14, "#666", "M1792 640q0 26-19 45l-512 512q-19 19-45 19t-45-19-19-45v-256h-224q-98 0-175.5 6t-154 21.5-133 42.5-105.5 69.5-80 101-48.5 138.5-17.5 181q0 55 5 123 0 6 2.5 23.5t2.5 26.5q0 15-8.5 25t-23.5 10q-16 0-28-17-7-9-13-22t-13.5-30-10.5-24q-127-285-127-451 0-199 53-333 162-403 875-403h224v-256q0-26 19-45t45-19 45 19l512 512q19 19 19 45z"),
        reply_sm: a(14, 14, "#666", "M7 0v3.675a11.411 11.411 0 0 1-2.135-.244 10.511 10.511 0 0 1-1.983-.635 5.92 5.92 0 0 1-1.715-1.13A4.975 4.975 0 0 1 0 .012c.047 1.075.206 2.045.479 2.912A7.68 7.68 0 0 0 1.686 5.28c.533.704 1.248 1.266 2.147 1.685.898.42 1.954.66 3.167.726V11l7-5.53L7 0", "0 0 14 11"),
        edit: a(14, 14, "#666", "M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"),
        trash: a(14, 14, "#666", "M704 1376v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm-544-992h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"),
        like: a(14, 14, "#666", "M0,535.5h102v-306H0V535.5z M561,255c0-28.05-22.95-51-51-51H349.35l25.5-117.3c0-2.55,0-5.1,0-7.65    c0-10.2-5.1-20.4-10.199-28.05L336.6,25.5L168.3,193.8c-10.2,7.65-15.3,20.4-15.3,35.7v255c0,28.05,22.95,51,51,51h229.5    c20.4,0,38.25-12.75,45.9-30.6l76.5-181.051c2.55-5.1,2.55-12.75,2.55-17.85v-51H561C561,257.55,561,255,561,255z", "0 0 561 561"),
        unlike: a(14, 14, "#666", "M357,25.5H127.5c-20.4,0-38.25,12.75-45.9,30.6L5.1,237.15C2.55,242.25,0,247.35,0,255v48.45l0,0V306    c0,28.05,22.95,51,51,51h160.65l-25.5,117.3c0,2.55,0,5.101,0,7.65c0,10.2,5.1,20.399,10.2,28.05l28.05,25.5l168.3-168.3    c10.2-10.2,15.3-22.95,15.3-35.7v-255C408,48.45,385.05,25.5,357,25.5z M459,25.5v306h102v-306H459z",
            "0 0 561 561"),
        paperclip: a(14, 14, "#666", "M1596 1385q0 117-79 196t-196 79q-135 0-235-100l-777-776q-113-115-113-271 0-159 110-270t269-111q158 0 273 113l605 606q10 10 10 22 0 16-30.5 46.5t-46.5 30.5q-13 0-23-10l-606-607q-79-77-181-77-106 0-179 75t-73 181q0 105 76 181l776 777q63 63 145 63 64 0 106-42t42-106q0-82-63-145l-581-581q-26-24-60-24-29 0-48 19t-19 48q0 32 25 59l410 410q10 10 10 22 0 16-31 47t-47 31q-12 0-22-10l-410-410q-63-61-63-149 0-82 57-139t139-57q88 0 149 63l581 581q100 98 100 235z", "0 0 1792 1792"),
        clock: a(14, 14, "#666", "M1024 544v448q0 14-9 23t-23 9h-320q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h224v-352q0-14 9-23t23-9h64q14 0 23 9t9 23zm416 352q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z", "0 0 1792 1792"),
        menu: a(14, 14, "#666", "M1664 1344v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45zm0-512v128q0 26-19 45t-45 19h-1408q-26 0-45-19t-19-45v-128q0-26 19-45t45-19h1408q26 0 45 19t19 45z",
            "0 0 1792 1792"),
        link: a(14, 14, "#666", "M1520 1216q0-40-28-68l-208-208q-28-28-68-28-42 0-72 32 3 3 19 18.5t21.5 21.5 15 19 13 25.5 3.5 27.5q0 40-28 68t-68 28q-15 0-27.5-3.5t-25.5-13-19-15-21.5-21.5-18.5-19q-33 31-33 73 0 40 28 68l206 207q27 27 68 27 40 0 68-26l147-146q28-28 28-67zm-703-705q0-40-28-68l-206-207q-28-28-68-28-39 0-68 27l-147 146q-28 28-28 67 0 40 28 68l208 208q27 27 68 27 42 0 72-31-3-3-19-18.5t-21.5-21.5-15-19-13-25.5-3.5-27.5q0-40 28-68t68-28q15 0 27.5 3.5t25.5 13 19 15 21.5 21.5 18.5 19q33-31 33-73zm895 705q0 120-85 203l-147 146q-83 83-203 83-121 0-204-85l-206-207q-83-83-83-203 0-123 88-209l-88-88q-86 88-208 88-120 0-204-84l-208-208q-84-84-84-204t85-203l147-146q83-83 203-83 121 0 204 85l206 207q83 83 83 203 0 123-88 209l88 88q86-88 208-88 120 0 204 84l208 208q84 84 84 204z",
            "0 0 1792 1792"),
        share: a(14, 14, "#666", "M1344 1024q133 0 226.5 93.5t93.5 226.5-93.5 226.5-226.5 93.5-226.5-93.5-93.5-226.5q0-12 2-34l-360-180q-92 86-218 86-133 0-226.5-93.5t-93.5-226.5 93.5-226.5 226.5-93.5q126 0 218 86l360-180q-2-22-2-34 0-133 93.5-226.5t226.5-93.5 226.5 93.5 93.5 226.5-93.5 226.5-226.5 93.5q-126 0-218-86l-360 180q2 22 2 34t-2 34l360 180q92-86 218-86z", "0 0 1792 1792"),
        smile: a(14, 14, "#666", "M1262 1075q-37 121-138 195t-228 74-228-74-138-195q-8-25 4-48.5t38-31.5q25-8 48.5 4t31.5 38q25 80 92.5 129.5t151.5 49.5 151.5-49.5 92.5-129.5q8-26 32-38t49-4 37 31.5 4 48.5zm-494-435q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm512 0q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm256 256q0-130-51-248.5t-136.5-204-204-136.5-248.5-51-248.5 51-204 136.5-136.5 204-51 248.5 51 248.5 136.5 204 204 136.5 248.5 51 248.5-51 204-136.5 136.5-204 51-248.5zm128 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z",
            "0 0 1792 1792"),
        image: a(14, 14, "#666", "M576 576q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1024 384v448h-1408v-192l320-320 160 160 512-512zm96-704h-1600q-13 0-22.5 9.5t-9.5 22.5v1216q0 13 9.5 22.5t22.5 9.5h1600q13 0 22.5-9.5t9.5-22.5v-1216q0-13-9.5-22.5t-22.5-9.5zm160 32v1216q0 66-47 113t-113 47h-1600q-66 0-113-47t-47-113v-1216q0-66 47-113t113-47h1600q66 0 113 47t47 113z", "0 0 1792 1792"),
        search: a(14, 14, "#666", "M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z",
            "0 0 1792 1792"),
        signin: a(14, 14, "#666", "M1312 896q0 26-19 45l-544 544q-19 19-45 19t-45-19-19-45v-288h-448q-26 0-45-19t-19-45v-384q0-26 19-45t45-19h448v-288q0-26 19-45t45-19 45 19l544 544q19 19 19 45zm352-352v704q0 119-84.5 203.5t-203.5 84.5h-320q-13 0-22.5-9.5t-9.5-22.5q0-4-1-20t-.5-26.5 3-23.5 10-19.5 20.5-6.5h320q66 0 113-47t47-113v-704q0-66-47-113t-113-47h-312l-11.5-1-11.5-3-8-5.5-7-9-2-13.5q0-4-1-20t-.5-26.5 3-23.5 10-19.5 20.5-6.5h320q119 0 203.5 84.5t84.5 203.5z", "0 0 1792 1792"),
        comments: a(14,
            14, "#666", "M1408 768q0 139-94 257t-256.5 186.5-353.5 68.5q-86 0-176-16-124 88-278 128-36 9-86 16h-3q-11 0-20.5-8t-11.5-21q-1-3-1-6.5t.5-6.5 2-6l2.5-5 3.5-5.5 4-5 4.5-5 4-4.5q5-6 23-25t26-29.5 22.5-29 25-38.5 20.5-44q-124-72-195-177t-71-224q0-139 94-257t256.5-186.5 353.5-68.5 353.5 68.5 256.5 186.5 94 257zm384 256q0 120-71 224.5t-195 176.5q10 24 20.5 44t25 38.5 22.5 29 26 29.5 23 25q1 1 4 4.5t4.5 5 4 5 3.5 5.5l2.5 5 2 6 .5 6.5-1 6.5q-3 14-13 22t-22 7q-50-7-86-16-154-40-278-128-90 16-176 16-271 0-472-132 58 4 88 4 161 0 309-45t264-129q125-92 192-212t67-254q0-77-23-152 129 71 204 178t75 230z",
            "0 0 1792 1792"),
        angle_down: a(14, 14, "#666", "M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z", "0 0 1792 1792"),
        code: a(14, 14, null, "M553 1399l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23t-10 23l-393 393 393 393q10 10 10 23t-10 23zm591-1067l-373 1291q-4 13-15.5 19.5t-23.5 2.5l-62-17q-13-4-19.5-15.5t-2.5-24.5l373-1291q4-13 15.5-19.5t23.5-2.5l62 17q13 4 19.5 15.5t2.5 24.5zm657 651l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23t-10 23z"),
        wordpress: a(14, 14, null, "M127 896q0-163 67-313l367 1005q-196-95-315-281t-119-411zm1288-39q0 19-2.5 38.5t-10 49.5-11.5 44-17.5 59-17.5 58l-76 256-278-826q46-3 88-8 19-2 26-18.5t-2.5-31-28.5-13.5l-205 10q-75-1-202-10-12-1-20.5 5t-11.5 15-1.5 18.5 9 16.5 19.5 8l80 8 120 328-168 504-280-832q46-3 88-8 19-2 26-18.5t-2.5-31-28.5-13.5l-205 10q-7 0-23-.5t-26-.5q105-160 274.5-253.5t367.5-93.5q147 0 280.5 53t238.5 149h-10q-55 0-92 40.5t-37 95.5q0 12 2 24t4 21.5 8 23 9 21 12 22.5 12.5 21 14.5 24 14 23q63 107 63 212zm-506 106l237 647q1 6 5 11-126 44-255 44-112 0-217-32zm661-436q95 174 95 369 0 209-104 385.5t-279 278.5l235-678q59-169 59-276 0-42-6-79zm-674-527q182 0 348 71t286 191 191 286 71 348-71 348-191 286-286 191-348 71-348-71-286-191-191-286-71-348 71-348 191-286 286-191 348-71zm0 1751q173 0 331.5-68t273-182.5 182.5-273 68-331.5-68-331.5-182.5-273-273-182.5-331.5-68-331.5 68-273 182.5-182.5 273-68 331.5 68 331.5 182.5 273 273 182.5 331.5 68z"),
        joomla: a(14, 14, null, "M1198 1073l-160 160-151 152-30 30q-65 64-151.5 87t-171.5 2q-16 70-72 115t-129 45q-85 0-145-60.5t-60-145.5q0-72 44.5-128t113.5-72q-22-86 1-173t88-152l12-12 151 152-11 11q-37 37-37 89t37 90q37 37 89 37t89-37l30-30 151-152 161-160zm-341-682l12 12-152 152-12-12q-37-37-89-37t-89 37-37 89.5 37 89.5l29 29 152 152 160 160-151 152-161-160-151-152-30-30q-68-67-90-159.5t5-179.5q-70-15-115-71t-45-129q0-85 60-145.5t145-60.5q76 0 133.5 49t69.5 123q84-20 169.5 3.5t149.5 87.5zm807 1067q0 85-60 145.5t-145 60.5q-74 0-131-47t-71-118q-86 28-179.5 6t-161.5-90l-11-12 151-152 12 12q37 37 89 37t89-37 37-89-37-89l-30-30-152-152-160-160 152-152 160 160 152 152 29 30q64 64 87.5 150.5t2.5 171.5q76 11 126.5 68.5t50.5 134.5zm-2-1124q0 77-51 135t-127 69q26 85 3 176.5t-90 158.5l-12 12-151-152 12-12q37-37 37-89t-37-89-89-37-89 37l-30 30-152 152-160 160-152-152 161-160 152-152 29-30q67-67 159-89.5t178 3.5q11-75 68.5-126t135.5-51q85 0 145 60.5t60 145.5z"),
        drupal: a(14, 14, null, "M1295 1586q-5-19-24-5-30 22-87 39t-131 17q-129 0-193-49-5-4-13-4-11 0-26 12-7 6-7.5 16t7.5 20q34 32 87.5 46t102.5 12.5 99-4.5q41-4 84.5-20.5t65-30 28.5-20.5q12-12 7-29zm-39-115q-19-47-39-61-23-15-76-15-47 0-71 10-29 12-78 56-26 24-12 44 9 8 17.5 4.5t31.5-23.5q3-2 10.5-8.5t10.5-8.5 10-7 11.5-7 12.5-5 15-4.5 16.5-2.5 20.5-1q27 0 44.5 7.5t23 14.5 13.5 22q10 17 12.5 20t12.5-1q23-12 14-34zm355-281q0-22-5-44.5t-16.5-45-34-36.5-52.5-14q-33 0-97 41.5t-129 83.5-101 42q-27 1-63.5-19t-76-49-83.5-58-100-49-111-19q-115 1-197 78.5t-84 178.5q-2 112 74 164 29 20 62.5 28.5t103.5 8.5q57 0 132-32.5t134-71 120-70.5 93-31q26 1 65 31.5t71.5 67 68 67.5 55.5 32q35 3 58.5-14t55.5-63q28-41 42.5-101t14.5-106zm53-160q0 164-62 304.5t-166 236-242.5 149.5-290.5 54-293-57.5-247.5-157-170.5-241.5-64-302q0-89 19.5-172.5t49-145.5 70.5-118.5 78.5-94 78.5-69.5 64.5-46.5 42.5-24.5q14-8 51-26.5t54.5-28.5 48-30 60.5-44q36-28 58-72.5t30-125.5q129 155 186 193 44 29 130 68t129 66q21 13 39 25t60.5 46.5 76 70.5 75 95 69 122 47 148.5 19.5 177.5z"),
        facebook: b(64, 64, "0 0 100 100", "23,85", "M959 1524v-264h-157q-86 0 -116 -36t-30 -108v-189h293l-39 -296h-254v-759h-306v759h-255v296h255v218q0 186 104 288.5t277 102.5q147 0 228 -12z"),
        twitter: b(64, 64, "0 0 100 100", "9,80", "M1620 1128q-67 -98 -162 -167q1 -14 1 -42q0 -130 -38 -259.5t-115.5 -248.5t-184.5 -210.5t-258 -146t-323 -54.5q-271 0 -496 145q35 -4 78 -4q225 0 401 138q-105 2 -188 64.5t-114 159.5q33 -5 61 -5q43 0 85 11q-112 23 -185.5 111.5t-73.5 205.5v4q68 -38 146 -41 q-66 44 -105 115t-39 154q0 88 44 163q121 -149 294.5 -238.5t371.5 -99.5q-8 38 -8 74q0 134 94.5 228.5t228.5 94.5q140 0 236 -102q109 21 205 78q-37 -115 -142 -178q93 10 186 50z"),
        google: b(64, 64, "0 0 125 125", "5,95", "M1437 623q0 -208 -87 -370.5t-248 -254t-369 -91.5q-149 0 -285 58t-234 156t-156 234t-58 285t58 285t156 234t234 156t285 58q286 0 491 -192l-199 -191q-117 113 -292 113q-123 0 -227.5 -62t-165.5 -168.5t-61 -232.5t61 -232.5t165.5 -168.5t227.5 -62 q83 0 152.5 23t114.5 57.5t78.5 78.5t49 83t21.5 74h-416v252h692q12 -63 12 -122zM2304 745v-210h-209v-209h-210v209h-209v210h209v209h210v-209h209z"),
        linkedin: b(64, 64, "0 0 105 105", "14,83", "M349 911v-991h-330v991h330zM370 1217q1 -73 -50.5 -122t-135.5 -49h-2q-82 0 -132 49t-50 122q0 74 51.5 122.5t134.5 48.5t133 -48.5t51 -122.5zM1536 488v-568h-329v530q0 105 -40.5 164.5t-126.5 59.5q-63 0 -105.5 -34.5t-63.5 -85.5q-11 -30 -11 -81v-553h-329 q2 399 2 647t-1 296l-1 48h329v-144h-2q20 32 41 56t56.5 52t87 43.5t114.5 15.5q171 0 275 -113.5t104 -332.5z"),
        tumblr: b(64, 64, "0 0 100 100", "22,85", "M944 207l80 -237q-23 -35 -111 -66t-177 -32q-104 -2 -190.5 26t-142.5 74t-95 106t-55.5 120t-16.5 118v544h-168v215q72 26 129 69.5t91 90t58 102t34 99t15 88.5q1 5 4.5 8.5t7.5 3.5h244v-424h333v-252h-334v-518q0 -30 6.5 -56t22.5 -52.5t49.5 -41.5t81.5 -14 q78 2 134 29z"),
        instagram: b(64, 64, "0 0 105 105", "14,84.5", "M1362 110v648h-135q20 -63 20 -131q0 -126 -64 -232.5t-174 -168.5t-240 -62q-197 0 -337 135.5t-140 327.5q0 68 20 131h-141v-648q0 -26 17.5 -43.5t43.5 -17.5h1069q25 0 43 17.5t18 43.5zM1078 643q0 124 -90.5 211.5t-218.5 87.5q-127 0 -217.5 -87.5t-90.5 -211.5 t90.5 -211.5t217.5 -87.5q128 0 218.5 87.5t90.5 211.5zM1362 1003v165q0 28 -20 48.5t-49 20.5h-174q-29 0 -49 -20.5t-20 -48.5v-165q0 -29 20 -49t49 -20h174q29 0 49 20t20 49zM1536 1211v-1142q0 -81 -58 -139t-139 -58h-1142q-81 0 -139 58t-58 139v1142q0 81 58 139 t139 58h1142q81 0 139 -58t58 -139z"),
        soundcloud: a(64, 64, "#fff", "M26.791,154.715c-0.067,0.478-0.411,0.81-0.833,0.81c-0.436,0-0.781-0.334-0.837-0.814l-1.527-11.399  l1.527-11.598c0.056-0.482,0.401-0.817,0.837-0.817c0.42,0,0.768,0.335,0.833,0.814l1.811,11.601L26.791,154.715z M39.128,161.653  c-0.071,0.496-0.429,0.843-0.866,0.843c-0.445,0-0.812-0.355-0.869-0.848l-2.053-18.338c0,0,2.053-18.749,2.053-18.754  c0.061-0.488,0.428-0.842,0.869-0.842c0.44,0,0.797,0.343,0.87,0.842l2.333,18.754L39.128,161.653z M51.378,165.435  c-0.057,0.687-0.584,1.202-1.225,1.202c-0.646,0-1.177-0.516-1.227-1.202l-1.841-22.117l1.841-22.859  c0.052-0.69,0.579-1.207,1.227-1.207c0.64,0,1.168,0.517,1.225,1.201l2.093,22.865L51.378,165.435z M63.373,165.619  c-0.054,0.785-0.657,1.379-1.4,1.379c-0.754,0-1.354-0.594-1.404-1.383l-1.735-22.299l1.735-21.206  c0.048-0.788,0.649-1.384,1.404-1.384c0.746,0,1.349,0.591,1.4,1.374l1.973,21.216L63.373,165.619z M75.374,165.626v-0.007  c-0.05,0.872-0.743,1.562-1.582,1.562c-0.842,0-1.536-0.687-1.579-1.558l-1.636-22.298l1.636-34.501  c0.041-0.876,0.736-1.566,1.579-1.566c0.838,0,1.532,0.69,1.582,1.566l1.848,34.501L75.374,165.626z M87.372,165.482  c-0.044,0.977-0.819,1.743-1.758,1.743c-0.947,0-1.719-0.767-1.757-1.734l-1.529-22.158c0,0,1.526-42.393,1.526-42.396  c0.042-0.976,0.814-1.741,1.761-1.741c0.939,0,1.712,0.764,1.758,1.741l1.726,42.396L87.372,165.482z M99.617,165.123v0.003  c-0.038,1.174-0.964,2.097-2.114,2.097c-1.151,0-2.08-0.923-2.112-2.091l-1.318-21.794l1.315-47.481  c0.031-1.181,0.962-2.104,2.115-2.104c1.148,0,2.079,0.926,2.114,2.104l1.481,47.482L99.617,165.123z M111.869,164.799v-0.017  c-0.032,1.384-1.114,2.462-2.471,2.462c-1.361,0-2.448-1.082-2.475-2.446l-1.104-21.45l1.104-44.58  c0.027-1.383,1.116-2.463,2.475-2.463c1.356,0,2.438,1.081,2.471,2.459l1.239,44.585L111.869,164.799z M124.383,164.359v-0.025  c-0.022,1.558-1.289,2.822-2.827,2.822c-1.542,0-2.809-1.265-2.829-2.799l-1.161-21l1.159-57.859  c0.023-1.557,1.29-2.822,2.832-2.822c1.539,0,2.806,1.266,2.827,2.817l1.26,57.864L124.383,164.359z M206.723,167.344  c-0.458,0-71.129-0.035-71.194-0.044c-1.534-0.154-2.753-1.464-2.773-3.035V82.747c0.019-1.498,0.535-2.271,2.474-3.021  c4.986-1.928,10.635-3.069,16.43-3.069c23.678,0,43.09,18.16,45.133,41.306c3.057-1.282,6.415-1.994,9.936-1.994  c14.183,0,25.681,11.502,25.681,25.688C232.406,155.845,220.906,167.344,206.723,167.344z",
            "0 0 256 256"),
        vkontakte: b(64, 64, "0 0 110 110", "7,82", "M1917 1016q23 -64 -150 -294q-24 -32 -65 -85q-78 -100 -90 -131q-17 -41 14 -81q17 -21 81 -82h1l1 -1l1 -1l2 -2q141 -131 191 -221q3 -5 6.5 -12.5t7 -26.5t-0.5 -34t-25 -27.5t-59 -12.5l-256 -4q-24 -5 -56 5t-52 22l-20 12q-30 21 -70 64t-68.5 77.5t-61 58 t-56.5 15.5q-3 -1 -8 -3.5t-17 -14.5t-21.5 -29.5t-17 -52t-6.5 -77.5q0 -15 -3.5 -27.5t-7.5 -18.5l-4 -5q-18 -19 -53 -22h-115q-71 -4 -146 16.5t-131.5 53t-103 66t-70.5 57.5l-25 24q-10 10 -27.5 30t-71.5 91t-106 151t-122.5 211t-130.5 272q-6 16 -6 27t3 16l4 6 q15 19 57 19l274 2q12 -2 23 -6.5t16 -8.5l5 -3q16 -11 24 -32q20 -50 46 -103.5t41 -81.5l16 -29q29 -60 56 -104t48.5 -68.5t41.5 -38.5t34 -14t27 5q2 1 5 5t12 22t13.5 47t9.5 81t0 125q-2 40 -9 73t-14 46l-6 12q-25 34 -85 43q-13 2 5 24q17 19 38 30q53 26 239 24 q82 -1 135 -13q20 -5 33.5 -13.5t20.5 -24t10.5 -32t3.5 -45.5t-1 -55t-2.5 -70.5t-1.5 -82.5q0 -11 -1 -42t-0.5 -48t3.5 -40.5t11.5 -39t22.5 -24.5q8 -2 17 -4t26 11t38 34.5t52 67t68 107.5q60 104 107 225q4 10 10 17.5t11 10.5l4 3l5 2.5t13 3t20 0.5l288 2 q39 5 64 -2.5t31 -16.5z"),
        odnoklassniki: b(64, 64, "0 0 105 105", "20,84", "M640 629q-188 0 -321 133t-133 320q0 188 133 321t321 133t321 -133t133 -321q0 -187 -133 -320t-321 -133zM640 1306q-92 0 -157.5 -65.5t-65.5 -158.5q0 -92 65.5 -157.5t157.5 -65.5t157.5 65.5t65.5 157.5q0 93 -65.5 158.5t-157.5 65.5zM1163 574q13 -27 15 -49.5 t-4.5 -40.5t-26.5 -38.5t-42.5 -37t-61.5 -41.5q-115 -73 -315 -94l73 -72l267 -267q30 -31 30 -74t-30 -73l-12 -13q-31 -30 -74 -30t-74 30q-67 68 -267 268l-267 -268q-31 -30 -74 -30t-73 30l-12 13q-31 30 -31 73t31 74l267 267l72 72q-203 21 -317 94 q-39 25 -61.5 41.5t-42.5 37t-26.5 38.5t-4.5 40.5t15 49.5q10 20 28 35t42 22t56 -2t65 -35q5 -4 15 -11t43 -24.5t69 -30.5t92 -24t113 -11q91 0 174 25.5t120 50.5l38 25q33 26 65 35t56 2t42 -22t28 -35z"),
        mailru: '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="114" height="114" viewBox="0 0 114 114"><path fill="#fff" d="M41.93,30.296c-3.896,0-7.066,3.169-7.066,7.065s3.17,7.067,7.066,7.067s7.064-3.171,7.064-7.067  S45.825,30.296,41.93,30.296z"></path><path fill="#fff" d="M72.223,30.296c-3.896,0-7.065,3.169-7.065,7.065s3.17,7.067,7.065,7.067s7.064-3.171,7.064-7.067  S76.117,30.296,72.223,30.296z"></path><path fill="#fff" d="M96.324,77.49c0.299-1.112,0.146-2.273-0.428-3.271l-8.785-15.268c-0.769-1.332-2.199-2.16-3.738-2.16  c-0.75,0-1.492,0.199-2.146,0.576c-0.998,0.574-1.711,1.502-2.012,2.613c-0.299,1.111-0.146,2.272,0.428,3.271l0.703,1.226  l-0.342,0.494c-4.232,6.111-13.26,10.061-22.994,10.061c-9.67,0-18.668-3.911-22.926-9.965l-0.352-0.5l0.754-1.289  c1.199-2.049,0.507-4.694-1.543-5.896c-0.66-0.385-1.41-0.589-2.17-0.589c-1.528,0-2.955,0.816-3.725,2.133l-8.935,15.27  c-1.199,2.05-0.507,4.695,1.543,5.895c0.659,0.387,1.411,0.59,2.172,0.59c1.527,0,2.954-0.815,3.724-2.132l3.496-5.974l0.871,0.876  c6.447,6.481,16.32,10.2,27.09,10.2c10.778,0,20.658-3.725,27.104-10.215l0.879-0.885l3.436,5.969  c0.768,1.332,2.2,2.159,3.739,2.159c0.751,0,1.492-0.198,2.144-0.573C95.311,79.529,96.023,78.602,96.324,77.49z"></path></svg>',
        icon: function(b, e, f) {
            return a(e, e, f || "#666", c[b])
        }
    }
}();
var WPacTime = WPacTime || {
    getTime: function(a, b, c) {
        return "chat" == c ? this.getChatTime(a, b || "en") : c ? this.getFormatTime(a, c, b || "en") : this.getDefaultTime(a, b || "en")
    },
    getChatTime: function(a, b) {
        var c = ((new Date).getTime() - a) / 1E3 / 60 / 60,
            d = c / 24;
        return 24 > c ? this.getFormatTime(a, "HH:mm", b) : 365 > d ? this.getFormatTime(a, "dd.MM HH:mm", b) : this.getFormatTime(a, "yyyy.MM.dd HH:mm", b)
    },
    getDefaultTime: function(a, b) {
        return this.getTimeAgo(a, b)
    },
    getTimeAgo: function(a, b) {
        var c = ((new Date).getTime() - a) / 1E3,
            d = c / 60,
            e = d / 60,
            f = e / 24,
            g = f / 365;
        b = WPacTime.Messages[b] ? b : "en";
        return 45 > c ? WPacTime.Messages[b].second : 90 > c ? WPacTime.Messages[b].minute : 45 > d ? WPacTime.Messages[b].minutes(d) : 90 > d ? WPacTime.Messages[b].hour : 24 > e ? WPacTime.Messages[b].hours(e) : 48 > e ? WPacTime.Messages[b].day : 30 > f ? WPacTime.Messages[b].days(f) : 60 > f ? WPacTime.Messages[b].month : 365 > f ? WPacTime.Messages[b].months(f) : 2 > g ? WPacTime.Messages[b].year : WPacTime.Messages[b].years(g)
    },
    getTime12: function(a, b) {
        var c = new Date(a);
        return (c.getHours() % 12 ? c.getHours() % 12 : 12) + ":" + c.getMinutes() +
            (12 <= c.getHours() ? " PM" : " AM")
    },
    getFormatTime: function(a, b, c) {
        var d = new Date(a),
            e = {
                SS: d.getMilliseconds(),
                ss: d.getSeconds(),
                mm: d.getMinutes(),
                HH: d.getHours(),
                hh: (d.getHours() % 12 ? d.getHours() % 12 : 12) + (12 <= d.getHours() ? "PM" : "AM"),
                dd: d.getDate(),
                MM: d.getMonth() + 1,
                yyyy: d.getFullYear(),
                yy: String(d.getFullYear()).toString().substr(2, 2),
                ago: this.getTimeAgo(a, c),
                12: this.getTime12(a, c)
            };
        return b.replace(/(SS|ss|mm|HH|hh|DD|dd|MM|yyyy|yy|ago|12)/g, function(a, b) {
            var c = e[b];
            return 10 > c ? "0" + c : c
        })
    },
    declineNum: function(a,
        b, c, d) {
        return a + " " + this.declineMsg(a, b, c, d)
    },
    declineMsg: function(a, b, c, d, e) {
        var f = a % 10;
        return 1 == f && (1 == a || 20 < a) ? b : 1 < f && 5 > f && (20 < a || 10 > a) ? c : a ? d : e
    }
};
WPacTime.Messages = {
    ru: {
        second: "\u0442\u043e\u043b\u044c\u043a\u043e \u0447\u0442\u043e",
        minute: "\u043c\u0438\u043d\u0443\u0442\u0443 \u043d\u0430\u0437\u0430\u0434",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u043c\u0438\u043d\u0443\u0442\u0430 \u043d\u0430\u0437\u0430\u0434", "\u043c\u0438\u043d\u0443\u0442\u044b \u043d\u0430\u0437\u0430\u0434", "\u043c\u0438\u043d\u0443\u0442 \u043d\u0430\u0437\u0430\u0434")
        },
        hour: "\u0447\u0430\u0441 \u043d\u0430\u0437\u0430\u0434",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a),
                "\u0447\u0430\u0441 \u043d\u0430\u0437\u0430\u0434", "\u0447\u0430\u0441\u0430 \u043d\u0430\u0437\u0430\u0434", "\u0447\u0430\u0441\u043e\u0432 \u043d\u0430\u0437\u0430\u0434")
        },
        day: "\u0434\u0435\u043d\u044c \u043d\u0430\u0437\u0430\u0434",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0434\u0435\u043d\u044c \u043d\u0430\u0437\u0430\u0434", "\u0434\u043d\u044f \u043d\u0430\u0437\u0430\u0434", "\u0434\u043d\u0435\u0439 \u043d\u0430\u0437\u0430\u0434")
        },
        month: "\u043c\u0435\u0441\u044f\u0446 \u043d\u0430\u0437\u0430\u0434",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "\u043c\u0435\u0441\u044f\u0446 \u043d\u0430\u0437\u0430\u0434", "\u043c\u0435\u0441\u044f\u0446\u0430 \u043d\u0430\u0437\u0430\u0434", "\u043c\u0435\u0441\u044f\u0446\u0435\u0432 \u043d\u0430\u0437\u0430\u0434")
        },
        year: "\u0433\u043e\u0434 \u043d\u0430\u0437\u0430\u0434",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0433\u043e\u0434 \u043d\u0430\u0437\u0430\u0434", "\u0433\u043e\u0434\u0430 \u043d\u0430\u0437\u0430\u0434",
                "\u043b\u0435\u0442 \u043d\u0430\u0437\u0430\u0434")
        }
    },
    en: {
        second: "just now",
        minute: "1m ago",
        minutes: function(a) {
            return Math.round(a) + "m ago"
        },
        hour: "1h ago",
        hours: function(a) {
            return Math.round(a) + "h ago"
        },
        day: "day ago",
        days: function(a) {
            return Math.round(a) + " days ago"
        },
        month: "month ago",
        months: function(a) {
            return Math.round(a / 30) + " months ago"
        },
        year: "year ago",
        years: function(a) {
            return Math.round(a) + " years ago"
        }
    },
    uk: {
        second: "\u0442\u0456\u043b\u044c\u043a\u0438 \u0449\u043e",
        minute: "\u0445\u0432\u0438\u043b\u0438\u043d\u0443 \u0442\u043e\u043c\u0443",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0445\u0432\u0438\u043b\u0438\u043d\u0443 \u0442\u043e\u043c\u0443", "\u0445\u0432\u0438\u043b\u0438\u043d\u0438 \u0442\u043e\u043c\u0443", "\u0445\u0432\u0438\u043b\u0438\u043d \u0442\u043e\u043c\u0443")
        },
        hour: "\u0433\u043e\u0434\u0438\u043d\u0443 \u0442\u043e\u043c\u0443",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0433\u043e\u0434\u0438\u043d\u0443 \u0442\u043e\u043c\u0443", "\u0433\u043e\u0434\u0438\u043d\u0438 \u0442\u043e\u043c\u0443",
                "\u0433\u043e\u0434\u0438\u043d \u0442\u043e\u043c\u0443")
        },
        day: "\u0434\u0435\u043d\u044c \u0442\u043e\u043c\u0443",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0434\u0435\u043d\u044c \u0442\u043e\u043c\u0443", "\u0434\u043d\u0456 \u0442\u043e\u043c\u0443", "\u0434\u043d\u0456\u0432 \u0442\u043e\u043c\u0443")
        },
        month: "\u043c\u0456\u0441\u044f\u0446\u044c \u0442\u043e\u043c\u0443",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "\u043c\u0456\u0441\u044f\u0446\u044c \u0442\u043e\u043c\u0443",
                "\u043c\u0456\u0441\u044f\u0446\u0456 \u0442\u043e\u043c\u0443", "\u043c\u0456\u0441\u044f\u0446\u0456\u0432 \u0442\u043e\u043c\u0443")
        },
        year: "\u0440\u0456\u043a \u0442\u043e\u043c\u0443",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0440\u0456\u043a \u0442\u043e\u043c\u0443", "\u0440\u043e\u043a\u0438 \u0442\u043e\u043c\u0443", "\u0440\u043e\u043a\u0456\u0432 \u0442\u043e\u043c\u0443")
        }
    },
    ro: {
        second: "chiar acum",
        minute: "\u00een urm\u0103 minut",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a),
                "o minuta in urma", "minute in urma", "de minute in urma")
        },
        hour: "acum o ora",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "acum o ora", "ore in urma", "de ore in urma")
        },
        day: "o zi in urma",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "o zi in urma", "zile in urma", "de zile in urma")
        },
        month: "o luna in urma",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "o luna in urma", "luni in urma", "de luni in urma")
        },
        year: "un an in urma",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a),
                "un an in urma", "ani in urma", "de ani in urma")
        }
    },
    lv: {
        second: "Maz\u0101k par min\u016bti",
        minute: "Pirms min\u016btes",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "pirms min\u016btes", "pirms min\u016bt\u0113m", "pirms min\u016bt\u0113m")
        },
        hour: "pirms stundas",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "pirms stundas", "pirms stund\u0101m", "pirms stund\u0101m")
        },
        day: "pirms dienas",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "pirms dienas", "pirms dien\u0101m",
                "pirms dien\u0101m")
        },
        month: "pirms m\u0113ne\u0161a",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "pirms m\u0113ne\u0161a", "pirms m\u0113ne\u0161iem", "pirms m\u0113ne\u0161iem")
        },
        year: "pirms gada",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "pirms gada", "pirms gadiem", "pirms gadiem")
        }
    },
    lt: {
        second: "k\u0105 tik",
        minute: "prie\u0161 minut\u0119",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "minut\u0117 prie\u0161", "minut\u0117s prie\u0161", "minu\u010di\u0173 prie\u0161")
        },
        hour: "prie\u0161 valand\u0105",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "valanda prie\u0161", "valandos prie\u0161", "valand\u0173 prie\u0161")
        },
        day: "prie\u0161 dien\u0105",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "diena prie\u0161", "dienos prie\u0161", "dien\u0173 prie\u0161")
        },
        month: "prie\u0161 m\u0117nes\u012f",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "m\u0117nes\u012f prie\u0161", "m\u0117nesiai prie\u0161", "m\u0117nesi\u0173 prie\u0161")
        },
        year: "prie\u0161 metus",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "metai prie\u0161", "metai prie\u0161", "met\u0173 prie\u0161")
        }
    },
    kk: {
        second: "\u0431\u0456\u0440 \u043c\u0438\u043d\u0443\u0442\u0442\u0430\u043d \u0430\u0437 \u0443\u0430\u049b\u044b\u0442 \u0431\u04b1\u0440\u044b\u043d",
        minute: "\u0431\u0456\u0440 \u043c\u0438\u043d\u0443\u0442 \u0431\u04b1\u0440\u044b\u043d",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u043c\u0438\u043d\u0443\u0442 \u0431\u04b1\u0440\u044b\u043d",
                "\u043c\u0438\u043d\u0443\u0442 \u0431\u04b1\u0440\u044b\u043d", "\u043c\u0438\u043d\u0443\u0442 \u0431\u04b1\u0440\u044b\u043d")
        },
        hour: "\u0431\u0456\u0440 \u0441\u0430\u0493\u0430\u0442 \u0431\u04b1\u0440\u044b\u043d",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0441\u0430\u0493\u0430\u0442 \u0431\u04b1\u0440\u044b\u043d", "\u0441\u0430\u0493\u0430\u0442 \u0431\u04b1\u0440\u044b\u043d", "\u0441\u0430\u0493\u0430\u0442 \u0431\u04b1\u0440\u044b\u043d")
        },
        day: "\u0431\u0456\u0440 \u043a\u04af\u043d \u0431\u04b1\u0440\u044b\u043d",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u043a\u04af\u043d \u0431\u04b1\u0440\u044b\u043d", "\u043a\u04af\u043d \u0431\u04b1\u0440\u044b\u043d", "\u043a\u04af\u043d \u0431\u04b1\u0440\u044b\u043d")
        },
        month: "\u0431\u0456\u0440 \u0430\u0439 \u0431\u04b1\u0440\u044b\u043d",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "\u0430\u0439 \u0431\u04b1\u0440\u044b\u043d", "\u0430\u0439 \u0431\u04b1\u0440\u044b\u043d", "\u0430\u0439 \u0431\u04b1\u0440\u044b\u043d")
        },
        year: "\u0431\u0456\u0440 \u0436\u044b\u043b \u0431\u04b1\u0440\u044b\u043d",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0436\u044b\u043b \u0431\u04b1\u0440\u044b\u043d", "\u0436\u044b\u043b \u0431\u04b1\u0440\u044b\u043d", "\u0436\u044b\u043b \u0431\u04b1\u0440\u044b\u043d")
        }
    },
    ka: {
        second: "\u10ec\u10d0\u10db\u10d8\u10e1 \u10ec\u10d8\u10dc",
        minute: "\u10ec\u10e3\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u10ec\u10e3\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc", "\u10ec\u10e3\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc",
                "\u10ec\u10e3\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc")
        },
        hour: "\u10e1\u10d0\u10d0\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u10e1\u10d0\u10d0\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc", "\u10e1\u10d0\u10d0\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc", "\u10e1\u10d0\u10d0\u10d7\u10d8\u10e1 \u10ec\u10d8\u10dc")
        },
        day: "\u10d3\u10e6\u10d8\u10e1 \u10ec\u10d8\u10dc",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u10d3\u10e6\u10d8\u10e1 \u10ec\u10d8\u10dc",
                "\u10d3\u10e6\u10d8\u10e1 \u10ec\u10d8\u10dc", "\u10d3\u10e6\u10d8\u10e1 \u10ec\u10d8\u10dc")
        },
        month: "\u10d7\u10d5\u10d8\u10e1 \u10ec\u10d8\u10dc",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "\u10d7\u10d5\u10d8\u10e1 \u10ec\u10d8\u10dc", "\u10d7\u10d5\u10d8\u10e1 \u10ec\u10d8\u10dc", "\u10d7\u10d5\u10d8\u10e1 \u10ec\u10d8\u10dc")
        },
        year: "\u10ec\u10da\u10d8\u10e1 \u10ec\u10d8\u10dc",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u10ec\u10da\u10d8\u10e1 \u10ec\u10d8\u10dc",
                "\u10ec\u10da\u10d8\u10e1 \u10ec\u10d8\u10dc", "\u10ec\u10da\u10d8\u10e1 \u10ec\u10d8\u10dc")
        }
    },
    hy: {
        second: "\u0574\u056b \u0584\u0576\u056b \u057e\u0561\u0575\u0580\u056f\u0575\u0561\u0576 \u0561\u057c\u0561\u057b",
        minute: "\u0574\u0565\u056f \u0580\u0578\u057a\u0565 \u0561\u057c\u0561\u057b",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0580\u0578\u057a\u0565 \u0561\u057c\u0561\u057b", "\u0580\u0578\u057a\u0565 \u0561\u057c\u0561\u057b", "\u0580\u0578\u057a\u0565 \u0561\u057c\u0561\u057b")
        },
        hour: "\u0574\u0565\u056f \u056a\u0561\u0574 \u0561\u057c\u0561\u057b",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u056a\u0561\u0574 \u0561\u057c\u0561\u057b", "\u056a\u0561\u0574 \u0561\u057c\u0561\u057b", "\u056a\u0561\u0574 \u0561\u057c\u0561\u057b")
        },
        day: "\u0574\u0565\u056f \u0585\u0580 \u0561\u057c\u0561\u057b",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0585\u0580 \u0561\u057c\u0561\u057b", "\u0585\u0580 \u0561\u057c\u0561\u057b", "\u0585\u0580 \u0561\u057c\u0561\u057b")
        },
        month: "\u0574\u0565\u056f \u0561\u0574\u056b\u057d \u0561\u057c\u0561\u057b",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "\u0561\u0574\u056b\u057d \u0561\u057c\u0561\u057b", "\u0561\u0574\u056b\u057d \u0561\u057c\u0561\u057b", "\u0561\u0574\u056b\u057d \u0561\u057c\u0561\u057b")
        },
        year: "\u0574\u0565\u056f \u057f\u0561\u0580\u056b \u0561\u057c\u0561\u057b",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u057f\u0561\u0580\u056b \u0561\u057c\u0561\u057b", "\u057f\u0561\u0580\u056b \u0561\u057c\u0561\u057b",
                "\u057f\u0561\u0580\u056b \u0561\u057c\u0561\u057b")
        }
    },
    fr: {
        second: "tout \u00e0 l'heure",
        minute: "environ une minute",
        minutes: function(a) {
            return Math.round(a) + " minutes"
        },
        hour: "environ une heure",
        hours: function(a) {
            return "environ " + Math.round(a) + " heures"
        },
        day: "un jour",
        days: function(a) {
            return Math.round(a) + " jours"
        },
        month: "environ un mois",
        months: function(a) {
            return Math.round(a / 30) + " mois"
        },
        year: "environ un an",
        years: function(a) {
            return Math.round(a) + " ans"
        }
    },
    es: {
        second: "en este momento",
        minute: "hace un minuto",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "hace un minuto", "minutos atr\u00e1s", "minutos atr\u00e1s")
        },
        hour: "una hora atr\u00e1s",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "una hora atr\u00e1s", "horas atr\u00e1s", "horas atr\u00e1s")
        },
        day: "hace un d\u00eda",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "un d\u00eda atr\u00e1s", "d\u00edas atr\u00e1s", "d\u00edas atr\u00e1s")
        },
        month: "Hace un mes",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a /
                30), "un mes atr\u00e1s", "meses atr\u00e1s", "meses atr\u00e1s")
        },
        year: "Hace un a\u00f1o",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "hace un a\u00f1o", "a\u00f1os atr\u00e1s", "a\u00f1os atr\u00e1s")
        }
    },
    el: {
        second: "\u03bb\u03b9\u03b3\u03cc\u03c4\u03b5\u03c1\u03bf \u03b1\u03c0\u03cc \u03ad\u03bd\u03b1 \u03bb\u03b5\u03c0\u03c4\u03cc",
        minute: "\u03b3\u03cd\u03c1\u03c9 \u03c3\u03c4\u03bf \u03ad\u03bd\u03b1 \u03bb\u03b5\u03c0\u03c4\u03cc",
        minutes: function(a) {
            return Math.round(a) + " minutes"
        },
        hour: "\u03b3\u03cd\u03c1\u03c9 \u03c3\u03c4\u03b7\u03bd \u03bc\u03b9\u03b1 \u03ce\u03c1\u03b1",
        hours: function(a) {
            return "about " + Math.round(a) + " hours"
        },
        day: "\u03bc\u03b9\u03b1 \u03bc\u03ad\u03c1\u03b1",
        days: function(a) {
            return Math.round(a) + " days"
        },
        month: "\u03b3\u03cd\u03c1\u03c9 \u03c3\u03c4\u03bf\u03bd \u03ad\u03bd\u03b1 \u03bc\u03ae\u03bd\u03b1",
        months: function(a) {
            return Math.round(a / 30) + " months"
        },
        year: "\u03b3\u03cd\u03c1\u03c9 \u03c3\u03c4\u03bf\u03bd \u03ad\u03bd\u03b1 \u03c7\u03c1\u03cc\u03bd\u03bf",
        years: function(a) {
            return Math.round(a) + " years"
        }
    },
    de: {
        second: "soeben",
        minute: "vor einer Minute",
        minutes: function(a) {
            return "vor " + Math.round(a) + " Minuten"
        },
        hour: "vor einer Stunde",
        hours: function(a) {
            return "vor " + Math.round(a) + " Stunden"
        },
        day: "vor einem Tag",
        days: function(a) {
            return "vor " + Math.round(a) + " Tagen"
        },
        month: "vor einem Monat",
        months: function(a) {
            return "vor " + Math.round(a / 30) + " Monaten"
        },
        year: "vor einem Jahr",
        years: function(a) {
            return "vor " + Math.round(a) + " Jahren"
        }
    },
    be: {
        second: "\u043c\u0435\u043d\u0448 \u0437\u0430 \u0445\u0432\u0456\u043b\u0456\u043d\u0443 \u0442\u0430\u043c\u0443",
        minute: "\u0445\u0432\u0456\u043b\u0456\u043d\u0443 \u0442\u0430\u043c\u0443",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0445\u0432\u0456\u043b\u0456\u043d\u0430 \u0442\u0430\u043c\u0443", "\u0445\u0432\u0456\u043b\u0456\u043d\u044b \u0442\u0430\u043c\u0443", "\u0445\u0432\u0456\u043b\u0456\u043d \u0442\u0430\u043c\u0443")
        },
        hour: "\u0433\u0430\u0434\u0437\u0456\u043d\u0443 \u0442\u0430\u043c\u0443",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0433\u0430\u0434\u0437\u0456\u043d\u0443 \u0442\u0430\u043c\u0443",
                "\u0433\u0430\u0434\u0437\u0456\u043d\u044b \u0442\u0430\u043c\u0443", "\u0433\u0430\u0434\u0437\u0456\u043d \u0442\u0430\u043c\u0443")
        },
        day: "\u0434\u0437\u0435\u043d\u044c \u0442\u0430\u043c\u0443",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0434\u0437\u0435\u043d\u044c \u0442\u0430\u043c\u0443", "\u0434\u043d\u0456 \u0442\u0430\u043c\u0443", "\u0434\u0437\u0451\u043d \u0442\u0430\u043c\u0443")
        },
        month: "\u043c\u0435\u0441\u044f\u0446 \u0442\u0430\u043c\u0443",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a /
                30), "\u043c\u0435\u0441\u044f\u0446 \u0442\u0430\u043c\u0443", "\u043c\u0435\u0441\u044f\u0446\u0430 \u0442\u0430\u043c\u0443", "\u043c\u0435\u0441\u044f\u0446\u0430\u045e \u0442\u0430\u043c\u0443")
        },
        year: "\u0433\u043e\u0434 \u0442\u0430\u043c\u0443",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a), "\u0433\u043e\u0434 \u0442\u0430\u043c\u0443", "\u0433\u0430\u0434\u044b \u0442\u0430\u043c\u0443", "\u0433\u043e\u0434 \u0442\u0430\u043c\u0443")
        }
    },
    it: {
        second: "proprio ora",
        minute: "un minuto fa",
        minutes: function(a) {
            return WPacTime.declineNum(Math.round(a), "un minuto fa", "minuti fa", "minuti fa")
        },
        hour: "un'ora fa",
        hours: function(a) {
            return WPacTime.declineNum(Math.round(a), "un'ora fa", "ore fa", "ore fa")
        },
        day: "un giorno fa",
        days: function(a) {
            return WPacTime.declineNum(Math.round(a), "un giorno fa", "giorni fa", "giorni fa")
        },
        month: "un mese fa",
        months: function(a) {
            return WPacTime.declineNum(Math.round(a / 30), "un mese fa", "mesi fa", "mesi fa")
        },
        year: "un anno fa",
        years: function(a) {
            return WPacTime.declineNum(Math.round(a),
                "un anno fa", "anni fa", "anni fa")
        }
    }
};
var WPacStars = WPacStars || {
    html: '<span class="wp-stars">{{~it.stars :c}}{{?it.rating >= c}}<span class="wp-star" data-origin="star" data-color="{{=it.color}}" data-star="{{=c}}" style="display:inline-block;vertical-align:middle;padding:0 4px 0 0;line-height:20px;cursor:pointer;">{{=it.svg.icon("star", it.size || 24, it.color)}}</span>{{??}}{{?c - it.rating < 1}}<span class="wp-star" data-origin="star_half" data-color="{{=it.color}}" data-star="{{=c}}" style="display:inline-block;vertical-align:middle;padding:0 4px 0 0;line-height:20px;cursor:pointer;">{{=it.svg.icon("star_half", it.size || 24, it.color)}}</span>{{??}}<span class="wp-star" data-origin="star_o" data-color="#ccc" data-star="{{=c}}" style="display:inline-block;vertical-align:middle;padding:0 4px 0 0;line-height:20px;cursor:pointer;">{{=it.svg.icon("star_o", it.size || 24, "#ccc")}}</span>{{?}}{{?}}{{~}}</span>',
    main: function(a) {
        var b = this,
            c = (a.cnt || document).querySelector(a.el);
        c.innerHTML = this.stars_render(a.size, a.color, a.stars, a.sum, a.count);
        if (!a.readonly) {
            var c = c.querySelector(".wp-stars"),
                d = c.querySelectorAll(".wp-star");
            WPacFastjs.on(c, "mouseleave", function(a) {
                WPacFastjs.each(d, function(a) {
                    var b = a.querySelector("path");
                    a.setAttribute("data-hover", "");
                    b.setAttribute("d", WPacSVGIcon.path[a.getAttribute("data-origin")]);
                    b.setAttribute("fill", a.getAttribute("data-color"))
                })
            });
            WPacFastjs.onall(d, "mouseout",
                function() {
                    b.prevHover(a, this);
                    b.nextHover(a, this)
                });
            WPacFastjs.onall(d, "click", function() {
                var c = parseInt(this.getAttribute("data-star"));
                a.clickable && (this.parentNode.setAttribute("data-star", c), WPacFastjs.each(d, function(d) {
                    d.getAttribute("data-star") <= c ? (d.setAttribute("data-origin", "star"), d.setAttribute("data-color", b.color(a.color))) : (d.setAttribute("data-origin", "star_o"), d.setAttribute("data-color", "#ccc"))
                }));
                a.cb && a.cb(c)
            })
        }
    },
    stars_render: function(a, b, c, d, e) {
        return this.render("html", {
            size: a,
            color: this.color(b),
            stars: this.stars(c || 5),
            rating: this.rating(d, e)
        })
    },
    rating_render: function(a, b, c, d) {
        return this.render("html", {
            size: b,
            color: this.color(c),
            stars: this.stars(d || 5),
            rating: a
        })
    },
    color: function(a) {
        return a ? a.match(/^#[0-9a-zA-Z]+$/) ? a : "#" + a : "#ff9800"
    },
    render: function(a, b) {
        b.svg = WPacSVGIcon;
        return doT.template(this[a] || a)(b)
    },
    stars: function(a) {
        for (var b = [], c = 1; c <= a; c++) b.push(c);
        return b
    },
    rating: function(a, b) {
        b = b || 0;
        return 0 < b ? ((a || 0) / b).toFixed(1) : 0
    },
    prevHover: function(a, b) {
        if (b) {
            var c =
                b.getAttribute("data-hover");
            c && "off" != c || (c = b.querySelector("path"), c.setAttribute("d", WPacSVGIcon.path.star), c.setAttribute("fill", this.color(a.color)), b.setAttribute("data-hover", "on"));
            this.prevHover(a, WPacFastjs.prev(b))
        }
    },
    nextHover: function(a, b) {
        var c = WPacFastjs.next(b);
        if (c) {
            var d = c.getAttribute("data-hover");
            d && "on" != d || (d = c.querySelector("path"), d.setAttribute("d", WPacSVGIcon.path.star_o), d.setAttribute("fill", "#ccc"), c.setAttribute("data-hover", "off"));
            this.nextHover(a, c)
        }
    }
};
var GRPPlaceFinder = GRPPlaceFinder || {
    _HTML: '<div class="wp-gri"><div class="row"><div class="col-sm-12"><div class="form form400 form-horizontal"><h4 class="text-left"><span class="wp-step">1</span>Search Google for your location</h4><div class="form-group"><div class="col-sm-12"><input type="text" class="wp-place form-control" placeholder="Google Place Search Query"></div></div><div class="form-group wp-gkey-cnt" style="display:none"><div class="col-sm-12"><input type="text" class="wp-gkey form-control" placeholder="Google API Key" {{?it.google_api_key}}value="{{=it.google_api_key}}"{{?}}><small>Default limit of requests to Google Places API exceeded, to continue <a href="https://developers.google.com/places/web-service/get-api-key" target="_blank">get Google Places API key</a></small></div></div><div class="form-group"><div class="col-sm-12"><button class="wp-get-place btn btn-block btn-primary">Search Place</button></div></div><div class="form-group"><div class="col-sm-12"><h4 class="text-left"><span class="wp-step">2</span>Confirm Location</h4><div class="wp-places"></div></div></div><div class="form-group"><div class="col-sm-12"><h4 class="text-left"><span class="wp-step">3</span>Save Location</h4><div class="wp-reviews"></div></div></div></div></div>{{?it.post_content}}{{=it.post_content}}{{?}}</div></div>',
    _PLACE_HTML: '<div class="media-left"><img class="media-object" src="{{=it.place.icon}}" alt="{{=it.place.name}}" style="width:32px;height:32px;"></div><div class="media-body"><h5 class="media-heading">{{=it.place.name}}</h5><div>{{?it.place.rating}}<span class="wp-grating">{{=it.place.rating}}</span><span class="wp-gstars"></span>{{?}}</div><small class="text-muted">{{=it.place.formatted_address}}</small></div>',
    _REVIEW_HTML: '<div class="media-left"><img class="media-object" src="{{=it.review.profile_photo_url || it.defava}}" alt="{{=it.review.author_name}}" onerror="if(this.src!=\'{{=it.defava}}\')this.src=\'{{=it.defava}}\';"></div><div class="media-body"><div class="media-heading"><a href="{{=it.review.author_url}}" target="_blank">{{=it.review.author_name}}</a></div><div class="wp-gtime">{{=it.time}}</div><div class="wp-gtext"><span class="wp-gstars"></span> {{=it.text}}</div></div>',
    _TEXT_HTML: '{{!it.t}} {{?it.h}}<span class="wp-more">{{!it.h}}</span><span class="wp-more-toggle" onclick="this.previousSibling.className=\'\';this.textContent=\'\';">read more</span>{{?}}',
    main: function(a) {
        console.log('test');
        var b = this,
            c = document.getElementById(a.el || "wpac-greview-install");
        c.innerHTML = doT.template(b._HTML)({
            post_content: a.post_content,
            google_api_key: a.google_api_key
        });
        WPacFastjs.on2(c, ".wp-get-place", "click", function() {
            var d = c.querySelector(".wp-place").value;
            /^ChIJ.*$/.test(d) ? (c.querySelector(".wp-places").innerHTML =
                "", b.details(a, c, d, !0)) : b.textsearch(a, c, d);
            return !1
        });
        WPacFastjs.cbs(a, "ready")
    },
    textsearch: function(a, b, c) {
        var d = this,
            e = b.querySelector(".wp-gkey").value,
            f = this.getLang();
        jQuery.get(a.app_host, {
            cf_action: "textsearch",
            query: c,
            key: e,
            lang: f,
            _textsearch_wpnonce: jQuery(a.nonce).val()
        }, function(g) {
            if ("GOOGLE_COULDNT_CONNECT" == g.error) WPacXDM.post("https://embed.widgetpack.com", "https://app.widgetpack.com/widget/google-review/place", {
                query: c,
                key: e,
                lang: f
            }, function(c) {
                return d.textsearchCallback(a, b, c.error,
                    c)
            });
            else return d.textsearchCallback(a, b, g.error, g.places)
        }, "json")
    },
    textsearchCallback: function(a, b, c, d) {
        var e = b.querySelector(".wp-gkey").parentNode.parentNode;
        if ("OVER_QUERY_LIMIT" == c) WPacFastjs.show2(e), WPacFastjs.addcl(e, "has-error");
        else if (WPacFastjs.remcl(e, "has-error"), c = b.querySelector(".wp-places"), !d || 1 > d.length) c.innerHTML = '<div class="wp-place-info">Business place not found.<br><br>Please check that this place can be found in <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank">Google PlaceID Finder</a>, if so just a copy <b>Place ID</b> to a search field and search again.</div>';
        else {
            c.innerHTML = "";
            var f = this;
            WPacFastjs.each(d, function(c) {
                f.place(a, b, c, function(c, d) {
                    WPacFastjs.on(d, "click", function() {
                        var e = b.querySelector(".wp-place-info.wp-active");
                        WPacFastjs.remcl(e, "wp-active");
                        WPacFastjs.addcl(d, "wp-active");
                        f.details(a, b, c.place_id, !1)
                    })
                })
            });
            c.appendChild(f.powered_by_google(a))
        }
    },
    place: function(a, b, c, d) {
        a = b.querySelector(".wp-places");
        b = WPacFastjs.create("div", "wp-place-info media", c.formatted_address);
        b.innerHTML = doT.template(this._PLACE_HTML)({
            place: c
        });
        a.appendChild(b);
        c.rating && (b.querySelector(".wp-gstars").innerHTML = WPacStars.rating_render(c.rating, 16, "e7711b"));
        return d && d(c, b)
    },
    details: function(a, b, c, d) {
        var e = this,
            f = b.querySelector(".wp-gkey").value,
            g = this.getLang();
        jQuery.get(a.app_host, {
            cf_action: "details",
            placeid: c,
            key: f,
            lang: g,
            _textsearch_wpnonce: jQuery(a.nonce).val()
        }, function(h) {
            if ("GOOGLE_COULDNT_CONNECT" == h.error) WPacXDM.post("https://embed.widgetpack.com", "https://app.widgetpack.com/widget/google-review/review", {
                placeid: c,
                key: f,
                lang: g
            }, function(c) {
                return e.detailsCallback(a,
                    b, c, d, e.saveJSON)
            });
            else return e.detailsCallback(a, b, h, d, e.save)
        }, "json")
    },
    detailsCallback: function(a, b, c, d, e) {
        var f = b.querySelector(".wp-gkey").parentNode.parentNode;
        if ("OVER_QUERY_LIMIT" == c.error) WPacFastjs.show2(f), WPacFastjs.addcl(f, "has-error");
        else {
            WPacFastjs.remcl(f, "has-error");
            var g = b.querySelector(".wp-reviews");
            if (c.place && (!c.place.reviews || 1 > c.place.reviews.length)) g.innerHTML = '<div class="wp-place-info">There are no reviews yet for this business</div>';
            else {
                g.innerHTML = "";
                d && this.place(a,
                    b, c.place,
                    function(a, b) {
                        WPacFastjs.addcl(b, "wp-active")
                    });
                var h = this;
                WPacFastjs.each(c.place.reviews, function(b) {
                    var c = WPacFastjs.create("div", "wp-place-info media");
                    c.innerHTML = doT.template(h._REVIEW_HTML)({
                        review: b,
                        text: h.text(a, b.text),
                        time: WPacTime.getTime(parseInt(1E3 * b.time), a.lang, "ago"),
                        defava: "https://lh3.googleusercontent.com/-8hepWJzFXpE/AAAAAAAAAAI/AAAAAAAAAAA/I80WzYfIxCQ/s64-c/114307615494839964028.jpg"
                    });
                    g.appendChild(c);
                    c.querySelector(".wp-gstars").innerHTML = WPacStars.rating_render(b.rating,
                        14, "e7711b")
                });
                g.appendChild(h.powered_by_google(a));
                (d = WPacFastjs.next(g)) && WPacFastjs.rm(d);
                d = WPacFastjs.create("button", "btn btn-block btn-primary");
                d.innerHTML = "Confirm Location Changes";
                WPacFastjs.after(g, d);
                WPacFastjs.on(d, "click", function() {
                    e(a, b, c);
                    return !1
                })
            }
        }
    },
    save: function(a, b, c) {
        jQuery.post(a.app_host + "&cf_action=save", {
            placeid: c.place.place_id,
            key: b.querySelector(".wp-gkey").value,
            _textsearch_wpnonce: jQuery(a.nonce).val()
        }, function(d) {
            var e = b.querySelector(".wp-gkey").parentNode.parentNode;
            "OVER_QUERY_LIMIT" == d.error ? (WPacFastjs.show2(e), WPacFastjs.addcl(e, "has-error")) : (WPacFastjs.remcl(e, "has-error"), WPacFastjs.cbs(a, "add", c.place))
        }, "json")
    },
    saveJSON: function(a, b, c) {
        jQuery.post(a.app_host + "&cf_action=save_json", {
            place: JSON.stringify(c.place),
            reviews: JSON.stringify(c.place.reviews),
            _textsearch_wpnonce: jQuery(a.nonce).val()
        }, function(d) {
            var e = b.querySelector(".wp-gkey").parentNode.parentNode;
            "OVER_QUERY_LIMIT" == d.error ? (WPacFastjs.show2(e), WPacFastjs.addcl(e, "has-error")) : (WPacFastjs.remcl(e,
                "has-error"), WPacFastjs.cbs(a, "add", c.place))
        }, "json")
    },
    text: function(a, b) {
        var c = a.text_size || 100,
            d = b,
            e = !1;
        if (b && b.length > c) {
            var f = b.lastIndexOf(" ", c),
                f = 0 < f ? f : c;
            0 < f && (d = b.substring(0, f), e = b.substring(f, b.length))
        }
        return doT.template(this._TEXT_HTML)({
            t: d,
            h: e
        })
    },
    powered_by_google: function(a) {
        a = WPacFastjs.create("div", "wp-glogo");
        a.innerHTML = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJAAAAASCAYAAAC0PldrAAAIHElEQVR4Ae3ZBXDbWB7H8efglpmZGW0HlhzJDpSZmZkZ3W3s2DpmZmbmKx0zM/NdoGhotxTf9x9LHY027paW85v5bBRQopn32weqasqzk5Jw7BE9nHijHo5/Rw/HfqaHYl/keldZ8GJ7qBeqZ/6PNGlPUT5DeVKNqkyc18PJ6VDPtfEfnvUJz0dmpbwfmzMW6k5YFy96pBRTkCnL4MUDiy94oS0F+ZVZlD/5qxLLfNFkz0D0fBtfKOkpCcffrFfG6vVQ4hDUc6ypQLdDFmMdMiWIcjywaOHEB8zynPEFa1pCOWnHLw2Bej5oKtDzqEC+cGyoWZ5YaTDeGer57r4LRPLRCi40liy0QR4eVB5CG2QhU3KQneG+TM/qQhvk424L1Bwt73P22d9QoFD8bVB3KxVUWTX+gqW1mvvLtbr7Z7V+z6frNM/UlFIuKAD83I2v5y69eiL3y9dPZP/s2tdyPn39ZN7UVEq5oCz8x+WvjC/SQrEv8/u/z/O9siSUKNfC8agWSbrRaIH+p3lH1Pg976jxe38gz8JzzLM/g2QhpmIuIjCwB/0cg6HjGAxEsQQtIZmCtbBnLbY4BngZptkGaQmiMHAUhbCyEJMxCxGsh6QFltruOwIv7BmEfTAQwWwsvYMCTcMqGKat6AJJCXYjD1ZcWIdZsEdK8K50gZLroez4+l4G8j1PURlfAJWaMye7Vvd+otbvTVVr7hvVmvc/ci0YyNdAidTHVPa1EzmfuH4iN0WBbnD9H7lu8PXc10CJYDCVxanvffI8nAZvloQTNenZMXYt/YyJ5Y3tgar9nkCt5n2y4Tl073+5TprPEIYSknW2ARpkWosQOkFSiijK0BtuHME2ZGMMDLSDpBMMU29bYaIYBxfW4wBGojcmw8BIWM8Vwjq40QMubMR+231TYGAYJN1QhVUYgEHYgMgdFCiCBeiLYdiJI2iO9og6St4XBgZnKpAWjm2AsmNZO+E8jdlnqzrdvS49aO4fndWLekDVlbmHMoB/MQewAorCrDML86P6E816QD35lbyhzER/SZcouwJKq0oskd9PSX9aVnm5F1RJVbKQr13IVKCU251b7Xf/WwpcqxfMkVnngm9MW+sZ6gJeD24N1BHkwUouDmI28hDGBNjTDwZGIR9hFEOiYyu2YRIkY1GFhzAABvrCnpVYbyvQAeTAyiBHKa2sNUnmY5/jvnwcvYMCbXDMmG1QBR2SJdhu+5l52NXYMsqA7EsXI/5OKBvEhnMKK7LwtePpgYy9HIpB+lbDIGmFbihLTcAzs6FAuueDUJTkW0hdO5XrhrJc/1reTPk6BfsgZMY7iZS/KumFsmihxIFMBaoNeHxmiT/6H5+7Y41esJcl7K/pmdD9N64n4tZALYQzs7EDvTMMmgtBW7GWYyUk26DDj/1wYYHt+z7bUhixMXDMVqDFznG5zX1HIdmDGbDnTvdAGpzZhGWO/2n6ojnCKIIzDNClwQ1LRmU8ETCS3aEysU5r7FHmQckAyUD9sWJgPpSlNlA4SL7O0vJNqGtfz/6bFKX+SyofyvLk1/IGNcxAX8v9JpS8QpDfX/Ha+nwoC8vplIwFYq/TUFbN+w9Zxqr93vpq3fOFWn/BJFlioYQ1UEvgzHxsRQ8Y6NdIgY6jDBI3wugGA53QxVa+IAogeQwR9EB3h27IVGwfqjLc1xWSnZgNZ5bdQYECcGabrcgubMUCPIZjtztQWPsOZpjvPRq52A7KSatKTpV3QfxcXenL61tASUFk8M7q7gIoiywl6UF1fwBKCmLONAVQlhsn8+aYX/8AVEll4pQ8h7x7grLIRj9TgWo0z8Ppsnqv1eqeyP98hX2hZClrrEDH0QpWWuIYJiMbQcx1TNWjYWCQbY8TwQbshJXdWI+obdPdEwbGwJ7ipylQH2vZdN5nK9AMHEVzx1JUeQcF2uVY+rojimJYGY8q7MdEZIy8MGTwfmHuhf5JkTbJex/fyxJdtcpLDzNob5JNrbn/mQMl2ECvNpeKn1sDV6MVjGEz/U9zWSmFYuO82tzr/PzK6fy+UFdP5o6hNP9M742ySyFL1VJrDxSIXu4NJUunvAXPVCApCfufP5p7rg1y2pMZkeswX/u+zIawF0jtwyOmvThiK5UXBhbDgwkIY7mjVKthoBRWymE0MngLbXsrN+bbS5WhQC4sRggVjvtGQ9IWQexGMR7BfjxxBwV6AptRgBIcxU7kwkoODiOCtrhtZOZhkD52m3/KiFGs+VCAdYT3fsicAer5WIcU4DGghPzcta/nfKjhFPa13HrUybW5fBlQQk5hWmXi/fL3rNnuTk5hdX6Pl79/wTwNXrJOYbIXOh9w98atgVqAx7EPx7AUHWDPSGy2la0MOY38zCp0gZWuWNXIbJONAPYhhK0YDSsTocGZbJRmuM9KJ6zAceyHD489zYyxCGMwGYdts24LOLMWi3DHkQ0sg/QqWU5KwvEf4HPMDHseDcc6QTnJUiH7EGajz8g7GPYiH2EWKoOyk/c9vAeax6zzGcryAz5+5PrJ7DIoO37QxTufhRT0C+zLvst11FrC5JQG5f7IrJd5Pjzzx56Pzh4CJc76CnryHugVPMe3OcqfYTkLymkMCpk30RnTlG62jfQLIr5gKkf+wVY29lAWWULTM2HsMah70VSgu888bIULL4hw2pqRPhEmfiMbadmbyWzE/utqSSj2nznBVB7UvWgq0N2lLSIYhxdOWL5k+Xzq/it2mdcGAah7ZV00eQlgxvFRpNfJeyc+Bn2RK32h7sf/AesqcHB02e65AAAAAElFTkSuQmCC" alt="powered by Google">';
        return a
    },
    getLang: function() {
        var a = navigator;
        return (a.language || a.systemLanguage || a.userLanguage || "en").substr(0, 2).toLowerCase()
    }
};
