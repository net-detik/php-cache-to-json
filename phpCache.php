<?php
namespace cache;
class jsonCache{
  static $cacheSaveTime=60; //default 60 detik
  static $domain      ='https://net.detik.org/pub';
  
  //@--
	//fungsi untuk mencari selisih waktu
	private function selisihWaktu($params){
		$awal  = date_create($params['waktu_awal']);
		$akhir = date_create($params['waktu_akhir']); // waktu sekarang
		$diff  = date_diff( $awal, $akhir );
		return $diff;
	}
  
  private function cmkdir($params){
    if (!is_dir($params['folderFull'])) mkdir($params['folderFull'], 0770, true);
  }
  
  private function getCache($params){
    //$cache=file_get_contents($params['fileName'],FILE_USE_INCLUDE_PATH);
    //cek last modifi
		if (file_exists($params['fileName'])){
			 $lastModifi=date("Y-m-d H:i:s", filemtime($params['fileName']));
		}else{
			//return to create cache
			$callback['pesan']		='gagal';
			$callback['text_msg']	='No cache please Create cache';
			return;
		}
    
    //cek selisih jam sekarang dengan waktu cache terakhir diperbaharui
		$selisihWaktu=self::selisihWaktu(array('waktu_awal'=>$lastModifi,'waktu_akhir'=>Date('Y-m-d H:i:s')));
		$selisihDetik=(($selisihWaktu->i*60)+$selisihWaktu->s);
		//jika selisih masih dibawah cache_maxtime maka
		if($selisihDetik<=self::$cacheSaveTime){ 
			$callback['pesan']		='sukses';
			$callback['text_msg']	='Load cache';
			$callback['hasCached']	=$selisihDetik;
			$callback['cachedId']	=$params['fileName'];
			$callback['data']		=file_get_contents($fileName,FILE_USE_INCLUDE_PATH);
		}else{
			//return to create cache
			$callback['pesan']		='gagal';
			$callback['text_msg']	='No cache please Create cache';
			$callback['hasCached']	=$selisihDetik;
			$callback['cachedId']	=$params['fileName'];
		}
		return $callback;
  }
  
  private function createCache($params){
    //create cache
		$fileContent	=$params['fileContent'];
		$folderFull		=$params['folderFull'];
		$fileName		  =$params['fileName'];
		self::cmkdir(array('folderFull'=>$folderFull));
		file_put_contents($folderFull."".$fileName, $fileContent);
  }

  private function makeRequest ($params) {
     $url = self::$domain;
     $fieldsString = http_build_query($params);
     $ch = curl_init();
      if($method == 'POST'){
				 curl_setopt($ch,CURLOPT_POST, count($params));
         curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsString);
      }
      else{
         $url .= '?'.$fieldsString;
      }
            
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_HEADER , false);  // we want headers
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec ($ch);
            $return['response'] = json_decode($result,true);
            if($return['response'] == false)
            $return['response'] = $result;
            $return['status'] =curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close ($ch);
            return $return;
    }
  
  public function result($params){
    $request=self::makeRequest($params);
    return $request;
  }
}
