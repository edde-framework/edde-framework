var require,define;
(function(k){require=function(a){if(e[a]){var b=e[a];e[a]=null;l.apply(k,b)}if(d[a])return d[a];throw Error('Missing define("'+a+'", ...) { ... }');};var d={},e={},f=Object.prototype.hasOwnProperty,l=function(a,b,h){for(var g=[],c=0;c<b.length;c+=1)if("require"===b[c])g[c]=function(a){return require(a)};else if("exports"===b[c])g[c]=d[a]=f.call(d,a)?d[a]:{};else if(f.call(d,b[c])||f.call(e,b[c]))g[c]=require(b[c]);else throw Error("Requested ["+a+"] dependency has missing dependency ["+b[c]+"]");
h.apply(d[a],g)};define=function(a,b,h){!1===f.call(d,a)&&!1===f.call(e,a)&&(e[a]=[a,b,h])};define.amd={}})();
