<?php

class Page_model extends CI_Model{
	public function CreateOrUpdate($arv=[],$id=""){
		$row = $this->getInfoContent($id);
		$arv["url_rewrite"] = $this->renderURL($arv, @$row->id);
		if($row){
			$this->db->update("pages",$arv,["id" => $id]);
		}else{
			$arv["language"] = config_item("language");
			$this->db->insert("pages", $arv);
		}
	}


	public function getList($parentID="0", $only_parent=false, $sname=""){
		$this->db->where("language = '' OR language ='".$this->config->item("language")."'");
		$this->db->where("parent_id", $parentID);
		$data =  $this->db->get("pages")->result();

		if(!$only_parent){
			$data2 = [];
			
			foreach ($data as $key => $value) {
				if($this->countChildren($value->id)){
				
				$value->children = new stdClass;
				
				$value->children->parent_name = ($sname ? $sname."-" : "").$value->title;
				$value->children->item = $this->getList($value->id, false, $value->title);
				}
				$data2[] = $value;
				
			}
			$data = $data2;
		}

		return $data;
	}

	private function countChildren($parent_id){
		$this->db->where("parent_id", $parent_id);
		$data =  $this->db->get("pages")->num_rows();
		return ($data > 0 ? true :  false);
	}

	public function getInfoContent($id=null){
		$this->db->where("id",$id);
		return $this->db->get("pages")->row();
	}


	public function getContent($url){
		$this->db->where("url_rewrite",$url);
		$this->db->where("(language='' OR language='".$this->config->item("language")."')");
		return $this->db->get("pages")->row();
	}

	public function renderURL($arv, $id){
		$url = ($arv["url_rewrite"] ? $arv["url_rewrite"] : $arv["title"]);
		if($id){
			return url_title($url,'-',true);
		}else{

			return url_title($url,'-',true);
		}
	}


}