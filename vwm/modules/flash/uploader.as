package {
	// ui components
	import flash.display.Sprite;

	import fl.controls.DataGrid;
	import fl.controls.Button;
	import fl.controls.ProgressBar;
	import fl.controls.ProgressBarMode;

	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.net.URLRequestMethod;
	import flash.net.URLLoader;

	import flash.events.MouseEvent;
	import flash.events.Event;
	import flash.events.ProgressEvent;
	import flash.events.DataEvent;

	import flash.net.FileReferenceList;
	import flash.net.FileReference;

	import fl.controls.Label;
	import flash.display.Graphics;
	import flash.display.Shape;
	import flash.text.TextFieldAutoSize;
	import flash.system.Security;

	import fl.controls.ScrollBar;
	import fl.events.ScrollEvent;

	import flash.events.HTTPStatusEvent;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;

	import flash.net.FileFilter;

	import flash.external.ExternalInterface;

	import XML;
	import flash.display.LoaderInfo;

	public class uploader extends Sprite {

		private var fileRef:FileReferenceList;
		private var list:Array;
		private var totalList:Array;
		private var files_dg:DataGrid;
		private var browse_btn:Button;
		private var upload_btn:Button;
		private var pb:ProgressBar;
		private var pbList:Array = new Array();
		private var step:int=0;

		private var shape:Shape;
		private var shapeList:Array = new Array();
		private var l:Label;
		private var lList:Array = new Array();
		private var lStatus:Label;
		private var lStatusList:Array = new Array();
		private var remove_btn:Button;
		private var remove_btnList:Array = new Array();
		private var lError:Label;
		private var totalLoaded:Number=0;
		private var warningLabel:Label;

		private var limitNewMSDS:int=0;
		private var limitNewMemory:Number=0;
		
		private var domain:String;
		private var folder:String;


		public function uploader() {
			var paramObject:Object = LoaderInfo(this.root.loaderInfo).parameters;
            domain = String(paramObject["domain"]);
			folder = String(paramObject["folder"]);
			
			//flash.system.Security.allowDomain("*");
			flash.system.Security.allowDomain(domain);			

			fileRef = new FileReferenceList();
			
			
			//	getting current URL
//			var url:String;
//			if (ExternalInterface.available) {
//				url = ExternalInterface.call("window.location.href.toString");
//			} else {
//				url = loaderInfo.loaderURL;
//			}
			
			// chck caller. Allow only from domain
//			var allowedPattern:String = "^http(|s)://"+domain+"/";
//			var domainCheck:RegExp = new RegExp(allowedPattern, 'i');
//			if(domainCheck.test(url)) {
				iniUI();
				inifileRefListener();
//			} 
		}




		private function iniUI():void {

			browse_btn = new Button();
			browse_btn.label="Browse";
			browse_btn.move(10,10);
			addChild(browse_btn);

			upload_btn = new Button();
			upload_btn.label="Upload";
			upload_btn.move(120,10);
			upload_btn.enabled=false;
			addChild(upload_btn);

			browse_btn.addEventListener(MouseEvent.CLICK,this.browse);
			upload_btn.addEventListener(MouseEvent.CLICK,this.upload);

			//shape
			var tmpStep:int=0;
			for (var i:Number = 0; i < 10; i++) {
				tmpStep+=50;
				shape=new Shape  ;
				shape.graphics.beginFill(0xFFFFFF,0.9);
				shape.graphics.lineStyle(0,0xCCCCCC);
				shape.graphics.drawRect(10,tmpStep,500,50);
				shape.graphics.endFill();
				addChild(shape);
				shapeList.push(shape);
			}
			
			//warning
			warningLabel = new Label();
			warningLabel.width=400;
			warningLabel.text="VOC WEB MANAGER | MSDS UPLOADER";
			warningLabel.move(15,560);
			addChild(warningLabel);

		}

		private function browse(event:MouseEvent):void {
			trace("// browse");
			var docFiles:FileFilter=new FileFilter("Documents","*.pdf;*.doc");
			fileRef.browse([docFiles]);
			//fileRef.browse(); //all files
		}

		private function upload(event:MouseEvent):void {
			trace("// upload");
			// upload the files
			browse_btn.enabled=false;
			
			//get companyID
			var paramObj:Object = LoaderInfo(this.root.loaderInfo).parameters;
            var companyID = String(paramObj["companyID"]);
			
			
			for (var i:Number = 0; i < totalList.length; i++) {
				remove_btnList[i].enabled=false;

				var file=totalList[i];
				var request:URLRequest = new URLRequest();

				request.url="http://"+domain+"/"+folder+"/index.php";				

				var variables:URLVariables = new URLVariables();
				variables.action="msdsUploaderMain";
				variables.companyID = companyID;

				request.data=variables;
				request.method=URLRequestMethod.POST;

				trace("name: " + file.name);
				file.upload(request);
			}
			upload_btn.enabled=false;
		}


		private function inifileRefListener() {
			fileRef.addEventListener(Event.SELECT, this.selectHandler);
		}

		private function iniFileListener(file:FileReference):void {
			file.addEventListener(Event.OPEN,this.openHandler);
			file.addEventListener(ProgressEvent.PROGRESS,this.progressHandler);
			file.addEventListener(Event.COMPLETE,this.completeHandler);
			file.addEventListener(HTTPStatusEvent.HTTP_STATUS,this.HTTPErrorHandler);
			file.addEventListener(IOErrorEvent.IO_ERROR,this.IOErrorHandler);
			file.addEventListener(SecurityErrorEvent.SECURITY_ERROR,this.SecurityErrorHandler);
		}


		private function selectHandler(event:Event):void {
			trace("// selectHandler");

			// list of the file references
			list=fileRef.fileList.slice(0,10);
			if (step==0) {//first browse click
				totalList=list;
				var firstIndex:Number=0;
			} else {//more then one browse click // array_unique
				var tmpTotal:Array=totalList.concat();
				var tmpList:Array=list.concat();

				for (var i:Number=0; i<list.length; i++) {
					var exists:Boolean=false;
					for (var j:Number=0; j<totalList.length; j++) {
						if (totalList[j].name==list[i].name) {
							exists=true;
						}
					}
					if (! exists) {
						tmpTotal.push(list[i]);
					} else {
						for (var k:Number=0; k<tmpList.length; k++) {
							if (tmpList[k].name==list[i].name) {
								tmpList.splice(k,1);
							}
						}
					}
				}

				var firstIndex:Number=totalList.length;

				tmpTotal=tmpTotal.slice(0,10);
				totalList=tmpTotal.concat();
				list=tmpList.concat();
			}
			//trace ("List:"+totalList.length);
			if (list.length>0) {

				var totalSize:int=0;
				for (var i:Number = 0; i < totalList.length; i++) {
					totalSize+=totalList[i].size;
				}
				limitNewMSDS=totalList.length;
				limitNewMemory=Math.round((totalSize/1024/1024)*100)/100;

				checkLimits();				

					upload_btn.enabled=true;

					for (var i:Number = firstIndex; i < totalList.length; i++) {
						trace(i);

						var formattedFileName:String=totalList[i].name+" ("+Math.round(totalList[i].size/1024)+" kb)";
						trace(formattedFileName);
						step+=50;

						//Label
						l = new Label();
						l.width=300;
						l.text=formattedFileName;
						l.move(15,step+5);
						addChild(l);
						lList.push(l);

						lStatus = new Label();
						lStatus.text="Ready for upload";
						lStatus.move(300,step+32);
						addChild(lStatus);
						lStatusList.push(lStatus);

						//creating progress bar
						pb = new ProgressBar();
						pb.mode=ProgressBarMode.POLLED;

						pb.move(15,step+30);
						pb.width=385;
						//addChild(pb);
						pbList.push(pb);

						//creating remove button
						remove_btn = new Button();
						remove_btn.label="Remove";
						remove_btn.name=i.toString();
						remove_btn.move(405,step+15);
						addChild(remove_btn);

						remove_btnList.push(remove_btn);
						remove_btnList[remove_btnList.length-1].addEventListener(MouseEvent.CLICK,this.remove);

						iniFileListener(totalList[i]);
				}
			}
		}

		private function remove(event:MouseEvent) {
			trace("// remove");
			var btn:Button=Button(event.target);
			var i:Number=parseInt(btn.name);

			for (var j:Number = i; j < totalList.length-1; j++) {
				lList[j].text=lList[j+1].text;
			}
			
			//for vps limits			
			limitNewMSDS--;
			limitNewMemory -= Math.round((totalList[i].size/1024/1024)*100)/100;
			checkLimits();
			
			//removing items

			removeChild(lList[totalList.length-1]);
			removeChild(lStatusList[totalList.length-1]);
			//removeChild(pbList[totalList.length-1]);
			removeChild(remove_btnList[totalList.length-1]);

			lList.pop();
			lStatusList.pop();
			//pbList.pop();
			remove_btnList.pop();
			
			totalList.splice(i,1);

			step-=50;
			
			if (totalList.length==0) {				
				browse_btn.enabled=true;
				upload_btn.enabled=false;
				step=0;
			}						
		}


		private function openHandler(event:Event):void {
			var file:FileReference=FileReference(event.target);
			trace("// onOpenName: " + file.name);
			/*for (var i:Number = 0; i < totalList.length; i++) {
			if (totalList[i].name==file.name) {
			}
			}*/
		}


		private function progressHandler(event:ProgressEvent):void {
			var file:FileReference=FileReference(event.target);
			trace("// onProgress: name=" + file.name + " bytesLoaded=" + event.bytesLoaded + " bytesTotal=" + event.bytesTotal);
			for (var i:Number = 0; i < totalList.length; i++) {
				if (totalList[i].name==file.name) {
					var percentDone = Math.round((event.bytesLoaded / event.bytesTotal) * 100);
					lStatusList[i].text="Uploading: "+percentDone+"%";
					pbList[i].source=event;
					addChild(pbList[i]);
				}
			}
		}


		private function completeHandler(event:Event):void {
			var file:FileReference=FileReference(event.target);
			trace("// onComplete: " + file.name);
			for (var i:Number = 0; i < totalList.length; i++) {
				if (totalList[i].name==file.name) {
					lStatusList[i].text="Done";
					totalLoaded++;
					if (totalLoaded==totalList.length) {
						var jsCall:Object=ExternalInterface.call("addSheetToAssignStep",file.name,true);
						//var jsCallFinish:Object=ExternalInterface.call("lastFileUploaded",file.name);
					} else {
						var jsCall:Object=ExternalInterface.call("addSheetToAssignStep",file.name,false);
					}
				}
			}
		}

		private function HTTPErrorHandler(event:HTTPStatusEvent):void {
			lError = new Label();
			lError.width=600;
			lError.text=""+event.status;
			trace(event);
			lError.move(230,step);
			addChild(lError);
		}

		private function IOErrorHandler(event:IOErrorEvent):void {
			lError = new Label();
			lError.width=600;
			lError.text=""+event;
			trace(event);
			lError.move(230,step+10);
			addChild(lError);
		}

		private function SecurityErrorHandler(event:SecurityErrorEvent):void {
			lError = new Label();
			lError.width=600;
			lError.text=""+event;
			trace(event);
			lError.move(230,step+20);
			addChild(lError);
		}

		private function checkLimits():void {
			var xmlLoader:URLLoader = new URLLoader();
			var xmlData:XML = new XML();

			xmlLoader.addEventListener(Event.COMPLETE, LoadXML);
			//xmlLoader.load(new URLRequest("http://192.168.1.68/voc_src/modules/resources/bridge/bridge.xml?"+Math.random()));
			xmlLoader.load(new URLRequest("http://"+domain+"/bridge/bridge.xml?"+Math.random()));
		}

		function LoadXML(e:Event):void {
			var xmlData:XML=new XML(e.target.data);
			//var companyID=this.loaderInfo.parameters.companyID;
			
			var paramObj:Object = LoaderInfo(this.root.loaderInfo).parameters;
            var companyID = String(paramObj["companyID"]);
			trace(companyID);
			if (companyID != "0") {//not super user
				var MSDSLimits:XMLList = xmlData.customers.customer.(@id == companyID).limit.(limit_id == "1");
				var memoryLimits:XMLList = xmlData.customers.customer.(@id == companyID).limit.(limit_id == "2");
				compareLimits(MSDSLimits,memoryLimits,companyID);
			}			
		}

		function compareLimits(MSDSLimits:XMLList, memoryLimits:XMLList, customerID:String) {			
			if (domain=="vocwebmanager.com" ||domain=="vocwebmanager.co.uk")
			{
				if ((parseInt(MSDSLimits.current_value)+limitNewMSDS > parseInt(MSDSLimits.max_value)) || (parseInt(memoryLimits.current_value)+limitNewMemory > parseInt(memoryLimits.max_value))) {
					upload_btn.enabled=false;
					trace ("disable");
					warningLabel.text="You cannot add new products according to Billing Plan";
				} else {
					trace ("enable");
					upload_btn.enabled=true;
					warningLabel.text="VOC WEB MANAGER | MSDS UPLOADER";
				}
			}
			else
			{
				upload_btn.enabled=true;
				warningLabel.text="VOC WEB MANAGER | MSDS UPLOADER";
			}
		}		
	}
}