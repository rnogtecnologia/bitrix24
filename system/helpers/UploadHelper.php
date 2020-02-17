<?php
	class UploadHelper{
		protected $path = 'uploads/', $file, $fileName, $fileTmpName;
		public function setPath( $path ){
			$this->path = $path;
		}
		public function setFile( $file ){
			$this->file = $file;
			$this->setFileName();
			$this->setFileTmpName();
		}
		public function setFileName(){
			$this->fileName = $this->file['name'];
		}
		public function setFileTmpName(){
			$this->fileTmpName = $this->file['tmp_name'];
		}
		public function upload(){
			if ( move_uploaded_file($this->fileTmpName, $this->path . $this->fileName) ){
				return true;
			}else{
				return false;
			}
			$this->fileName = $this->file['name'];
		}
	}
