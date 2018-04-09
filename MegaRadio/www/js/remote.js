function keyCode(ir_code,dune){
 var xmlhttp=null;
 var here=dune.split('.');
 
 if (window.ActiveXObject){
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
 }else if (window.XMLHttpRequest){
  xmlhttp=new XMLHttpRequest();
 }
 if (xmlhttp!=null){
  xmlhttp.open("GET","http://"+dune+"/cgi-bin/do?cmd=ir_code&ir_code="+ir_code,true);
  xmlhttp.onreadystatechange=function(){
   if (xmlhttp.readyState==4){
   }
  };
  xmlhttp.send(null);
 }else{
  alert("This browser does not support XMLHTTP");
 }
}
function playCode(streaming_url,dune){
 var xmlhttp=null;
 var here=dune.split('.');
 
 if (window.ActiveXObject){
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
 }else if (window.XMLHttpRequest){
  xmlhttp=new XMLHttpRequest();
 }
 if (xmlhttp!=null){
  xmlhttp.open("GET","http://"+dune+"/cgi-bin/plugins/MegaRadio/link?n1="+streaming_url,true);
  xmlhttp.onreadystatechange=function(){
   if (xmlhttp.readyState==4){
   }
  };
  xmlhttp.send(null);
 }else{
  alert("This browser does not support XMLHTTP");
 }
}