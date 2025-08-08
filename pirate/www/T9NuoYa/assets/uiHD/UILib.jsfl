fl.outputPanel.clear();
var patrn = new RegExp("^[a-zA-Z]{1}([a-zA-Z0-9]|[._])+(png|jpg|jpeg|bmp|gif)","i");

var baseUrl = "file:///D|/workSpace/assets/uiHD";
var flaUrl = "uiHD.fla";
var swfUrl = "uiHD.swf";
var logUrl = "UIHDLib.xml";
var linkList=[];

var dom = fl.openDocument(baseUrl+"/"+flaUrl);
var lib=dom.library;
var date = new Date();
//		new Date().getFullYear()
//		new Date().getMonth();
//		new Date().getDay()
//		new Date().getHours();
//		new Date().getMinutes()
//		new Date().getSeconds()
fl.trace("-------------:"+dom.path + ">  have been open----------");

toImport("");
toUpdate();
toPublish();

//?¨¦?¡Á????????????¡Á??-----------------------------------------------------------?¡§1??
//????¡¤??baseUrl,?»Ç"file:///F|";
//(1)??¡Á?+?¨®????»Çbtn.png???????????¡Á?
var nameUrl;
//(2)?¨¤??¡¤?????»Çfile:///C|/Users/vincent/Desktop/js/button????????????
var filePath;
//(3)?¨¤??¡¤?????»Çui/btn/,?????§Ø?????¨¤?????
var fileUrl;

function toImport(foldUrl){
	if(foldUrl!=""){
		filePath = baseUrl+"/"+foldUrl;
		fileUrl = foldUrl+"/";
	}else{
		filePath = baseUrl+foldUrl;
		fileUrl = foldUrl;
	}
	var fileList=FLfile.listFolder(filePath,"files");
	for(var i=0;i<fileList.length;i++){
		nameUrl = fileList[i];
		if(patrn.exec(nameUrl)){
			if(!lib.itemExists(fileUrl+nameUrl)){
				dom.importFile(filePath+"/"+nameUrl,true);
				lib.selectItem(nameUrl); 
				if(foldUrl!=""){
					lib.newFolder(foldUrl);
					lib.moveToFolder(foldUrl); 
				}
				fl.trace("???????----------------"+fileUrl+nameUrl);
			}else{
				fl.trace("???????----------------"+fileUrl+nameUrl);
				continue;
			}

		}else{
			fl.trace("¡¤?¡§????----------------"+fileUrl+nameUrl);
		}
	}
     var folderList=FLfile.listFolder(filePath,"directories");
     for(var j=0;j<folderList.length;j++){
		if(folderList[j]!=".svn"){
			 if(foldUrl!=""){
				  toImport(foldUrl+"/"+folderList[j]);
			 }else{
				  toImport(folderList[j]);
			 }
		}
     }
}


//?¨¦????????¨¹???¡Á??-----------------------------------------------------------?¡§2??
function toUpdate(){
//	var lib=dom.library;
	var itm;
	var start;
	var end;
	var fileUrl;
	var linkName;
	var filePath;
	for(var i in lib.items){
		itm = lib.items[i];
		if(!itm)continue;
		fileUrl=itm.name;
		filePath = baseUrl+"/"+fileUrl;
		if(FLfile.exists(filePath)){
			lib.updateItem(fileUrl);
			if(itm.itemType=="bitmap"){
				start = fileUrl.lastIndexOf("/")+1;
				end = fileUrl.indexOf(".");
				linkName = fileUrl.substring(start,end);
				if(!itm.linkageExportForAS){
					itm.linkageImportForRS=false;
	  				itm.linkageExportForAS=true;
	  				itm.linkageExportInFirstFrame=true;
	  				itm.linkageBaseClass = "flash.display.BitmapData";
	  				itm.linkageClassName=linkName;
					fl.trace("????¨¹??-------------"+fileUrl);
				}else{
					if(itm.linkageClassName!=linkName){
						itm.linkageClassName=linkName
					}
				}
				linkList.push(itm.linkageClassName);
			}
		}else{
			lib.deleteItem(fileUrl);
			fl.trace("??????--------------"+fileUrl);
		}
	}
}

//¡¤???swf,???¨²???¨¤???-----------------------------------------------------------?¡§3??
function toPublish(){
	dom.exportSWF(baseUrl+"/"+swfUrl);
	var b = true;//confirm("???????log????");
	if(b){
		fl.outputPanel.clear();
		//fl.trace("?¨¹?????-----------------:"+date.getFullYear()+"/"+(date.getMonth()+1)+"/"+date.getDate()+" "
		 //				    +date.getHours()+":"+date.getMinutes());
		//fl.trace("-------------------------------------------------------------------------------")
		//fl.trace("?????¨¤-----------------------"+linkList.length);
		linkList.sort();
		fl.trace("<root>");
		for(var i=0;i<linkList.length;i++){
			fl.trace("<item>"+linkList[i]+"</item>");
		}
		fl.trace("</root>");
		//fl.trace("over---------------------------------------------------------------------------");
		fl.outputPanel.save(baseUrl+"/"+logUrl);
	}
	fl.saveDocument(dom);
//	fl.closeDocument(dom);
}
