<?php
namespace pbol377;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\entity\EntityDamageEvent;

class Main extends PluginBase implements Listener{
	
	private static $instance = null;

    public static function getInstance(): Loader
    {
        return self::$instance;
    }
    
    public function onLoad(): void
    {
        if (self::$instance === null) self::$instance = $this;
    }
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents ($this, $this);
		$this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        $this->db = $this->data->getAll();
        if(!isset($this->db))$this->db=[];
        $this->cool = new Config($this->getDataFolder() . "cooltime.yml", Config::YAML);
        $this->ct = $this->cool->getAll();
        $this->cool2 = new Config($this->getDataFolder() . "cooltime2.yml", Config::YAML);
        $this->ct2 = $this->cool2->getAll();
        }
        
     public function onDisable(){
     	$this->save();
     }
     
     public function onJoin(PlayerJoinEvent $event){
     	$arr=array("cn"=>"없음", "time"=>0);
     	if(!isset($this->ct[$event->getPlayer()->getName()])){
     	
     	$this->ct[$event->getPlayer()->getName()][0] = $arr;
     $this-> save();
     }
     if(!isset($this->ct2[$event->getPlayer()->getName()])){
     	$this->ct2[$event->getPlayer()->getName()][0] = $arr;
     $this-> save();
     }
     }
     
     public function onDamange(EntityDamageEvent $event){
     	$cause = $event->getCause();
     if($cause === EntityDamageEvent::CAUSE_FALL) $event->setBaseDamage(0);;
     }
     
     public function onCommand(Commandsender $sender, Command $command, string $label, array $args) : bool{
	$name = $sender->getName();
	$cmd = $command->getName();
	$pf="§f[ §a특수효과 §f] §7";
	if (!$sender instanceof Player) {
		$sender->sendMessage("§c§lProhibited in Console");
		return true;
		}
	if($cmd == "효과부여"){
		if(!$sender->isOp()){
				$sender->sendMessage($pf."권한이 없습니다");
				return true;
				}
		if(!isset($args[0])){
			$sender->sendMessage($pf."\n/효과부여 <부여하기> [효과번호] [강도] [지속시간] [쿨타임] \n{$pf}대쉬의 경우 /효과부여 부여하기 [장비명] 대쉬 [대쉬칸수] [쿨타임] [방향 앞, 뒤, 왼쪽, 오른쪽, 앞좌, 뒤좌]\n{$pf}효과중복을 원하시면 [효과번호]란에 효과번호를 *을 중간에 넣고 [강도], [지속시간] 란에 강도, 지속시간을 *을 중간에 넣으십시오.\n{$pf}예) /효과부여 부여하기 1*5*6 1*1*1 5*5*5 0 ♤♤ 효과번호 1,5,6 강도가 1,1,1 지속시간이 5,5,5 쿨타임이 0초인 효과장비가 생성됩니다\n{$pf}/효과부여 <지급> [번호] \n{$pf}/효과부여 <삭제> [효과번호]\n{$pf}/효과부여 <목록> ");
			return true;
			}
		if($args[0]=="부여하기"){
			if(count($args)<6){$sender->sendMessage($pf."\n/효과부여 <부여하기> [효과번호] [강도] [지속시간] [쿨타임] \n{$pf}대쉬의 경우 /효과부여 부여하기 [장비명] 대쉬 [대쉬칸수] [쿨타임] [방향 앞, 뒤, 왼쪽, 오른쪽, 앞좌, 뒤좌]\n{$pf}효과중복을 원하시면 [효과번호]란에 효과번호를 *을 중간에 넣고 [강도], [지속시간] 란에 강도, 지속시간을 *을 중간에 넣으십시오.\n{$pf}예) /효과부여 부여하기 1*5*6 1*1*1 5*5*5 0 ♤♤ 효과번호 1,5,6 강도가 1,1,1 지속시간이 5,5,5 쿨타임이 0초인 효과장비가 생성됩니다\n{$pf}/효과부여 <지급> [번호] \n{$pf}/효과부여 <삭제> [효과번호]\n{$pf}/효과부여 <목록> "); return true;}
			$item=$sender->getInventory()->getItemInHand();
	        $id=$item->getId();
	        $dam=$item->getDamage();
	        if($args[2]=="대쉬"){
			$array=array("효과"=>$args[1].":".$args[2].":".$args[3].":".$args[4].":".$id.":".$dam.":".$args[5]);
			}
			else{
				$array=array("효과"=>$args[1].":".$args[2].":".$args[3].":".$args[4].":".$args[5].":".$id.":".$dam);
				}
			array_push($this->db,$array);
			$this->save();
			$sender->sendMessage($pf."효과부여가 완료되었습니다. 목록을 확인해주세요");
			$b[1]=$args[2];
			$b[2]=$args[3];
			$b[3]=$args[4];
			$b[6]=$args[5];
			$newitem = new Item($id, $dam, 1);
			if($args[2]!="대쉬"){
$a="§e[§7 효과 §e]§7\n◇ 효과: ".$b[1]."\n◇ 강도: ".$b[2]."\n◇ 지속시간: ".$args[4]."\n◇ 쿨타임: ".$args[5]."초";
}
			else{
				$a="§e[§7 효과 §e]§7\n◇ 효과: ".$b[1]."\n◇ 강도: ".$b[2]."\n◇ 쿨타임: ".$b[3]."초\n◇ 방향: ".$b[6];
				}
			$newitem->setCustomName($args[1]);
			$newitem->setLore([$a]);
			$sender->getInventory()->addItem($newitem);
			$sender->sendMessage($pf."성공적으로 지급되었습니다");
			return true;
			}
		else if($args[0]=="목록"){
			foreach($this->db as $key => $val){
				$val2=$this->db[$key]["효과"];
				$sender->sendMessage($pf."[ {$key} ] [$val2]");
				}
				return true;
			}
		else if($args[0]=="지급"){
			if(count($args)!=2){
				$sender->sendMessage($pf."/효과부여 지급 (번호)");
				return true;
				}
			if(!isset($this->db[$args[1]])){
				$sender->sendMessage($pf."{$args[1]}는 존재하지 않는 번호입니다");
				return true;
				}
			if(!is_numeric($args[1])){
				$sender->sendMessage($pf."숫자만 입력 가능합니다");
				return true;
				}
			$b=explode(":",$this->db[$args[1]]["효과"]);
			if($b[1]=="대쉬"){
			$newitem = new Item($b[4], $b[5], 1);
			$a="§e[§7 효과 §e]§7\n◇ 효과: ".$b[1]."\n◇ 강도: ".$b[2]."\n◇ 쿨타임: ".$b[3]."초\n◇ 방향: ".$b[6];
			}
			else{
				$newitem = new Item($b[5], $b[6], 1);
			$a="§e[§7 효과 §e]§7\n◇ 효과: ".$b[1]."\n◇ 강도: ".$b[2]."\n지속시간: ".$b[3]."\n◇ 쿨타임: ".$b[4]."초";
				}
			$newitem->setCustomName($b[0]);
			$newitem->setLore([$a]);
			$sender->getInventory()->addItem($newitem);
			$sender->sendMessage($pf."성공적으로 지급되었습니다");
			}
		else if($args[0]=="삭제"){
			if(!isset($this->db[$args[1]])){
				$sender->sendMessage($pf."{$args[1]}는 존재하지 않는 번호입니다");
				return true;
				}
			if(count($args)!=2){
				$sender->sendMessage($pf."/효과부여 삭제 (번호)");
				return true;
				}
			unset($this->db[$args[1]]);
			$sender->sendMessage($pf."성공적으로 아이템이 삭제되었습니다");
			$this->save();
			return true;
			}
		}
		return true;
	}
	
	public function dash( Player $player, float $size){
		$directionVector = $player->getDirectionVector()->multiply($size/2);
		$dx = $directionVector->getX();
		$dy = $directionVector->getY();
		$dz = $directionVector->getZ();
		$player->setMotion(new \pocketmine\math\Vector3($dx, -0.2, $dz));
	}
	
	public function Updash( Player $player, float $size) {
		$directionVector = $player->getDirectionVector()->multiply($size/2);
		$dx = $directionVector->getX();
		$dy = $directionVector->getY();
		$dz = $directionVector->getZ();
		$player->setMotion(new \pocketmine\math\Vector3($dx, 1*$size/2,$dz));
	}
	
	public function Bcdash( Player $player, float $size){
		$directionVector = $player->getDirectionVector()->multiply($size/2);
		$dx = $directionVector->getX();
		$dy = $directionVector->getY();
		$dz = $directionVector->getZ();
		$player->setMotion(new \pocketmine\math\Vector3(-$dx, -0.2, -$dz));
	}
	
	public function Ridash( Player $player, float $size){
		$directionVector = $player->getDirectionVector();
		$dx = round($directionVector->getX(), 2);
		$dy = round($directionVector->getY(),2);
		$dz = round($directionVector->getZ(),2);
		//var_dump($dx."::".$dy."::".$dz);
		$player->setMotion(new \pocketmine\math\Vector3(-$dz*$size/2, -0.2, $dx*$size/2));
	}//public
	
	public function Ledash( Player $player, float $size){
		$directionVector = $player->getDirectionVector();
		$dx = round($directionVector->getX(), 2);
		$dy = round($directionVector->getY(),2);
		$dz = round($directionVector->getZ(),2);
		//var_dump($dx."::".$dy."::".$dz);
		$player->setMotion(new \pocketmine\math\Vector3($dz*$size/2, -0.2, -$dx*$size/2));
	}//public
	
	public function addEffect(\pocketmine\entity\Creature $player, $id, $amplifier, $seconds){
		$effectType = \pocketmine\entity\Effect::getEffect($id);
		$player->addEffect(new \pocketmine\entity\EffectInstance($effectType, 20*$seconds, $amplifier, false));
	}
	
	public function onTouch(PlayerInteractEvent $event){
		$pf="§f[ §a특수효과 §f] §7";
		$player=$event->getPlayer();
		$name=$player->getName();
		//백터값 계산용
		$directionVector = $player->getDirectionVector();
		$dx = round($directionVector->getX(), 2);
		$dy = round($directionVector->getY(),2);
		$dz = round($directionVector->getZ(),2);
		
		$item=$player->getInventory()->getItemInHand();
        $cn=(string)$item->getCustomName();
        $id=$item->getId();
	    $dam=$item->getDamage();
	foreach($this->db as $key => $val){
		$val2=explode(":",$this->db[$key]["효과"]);
		if($val2[1]!="대쉬"){
			if($cn==$val2[0] && $id==$val2[5] && $dam==$val2[6]){
				$c=0;
				var_dump($this->ct);
				foreach($this->ct2[$name] as $key => $val){
					if($val["cn"]==$cn){
						$c=1;
						$nm=$val["cn"];
						$tm=$val["time"];
						$ky=$key;
						}
					}
				if($c==0){
array_push($this->ct2[$name], array("cn"=>$cn, "time"=>0));
}
else{
				if($tm-time()<=0){
					$val3=explode("*",$val2[1]);
					$kang=explode("*",$val2[2]);
					$jisuk=explode("*",$val2[3]);
					foreach($val3 as $key2 => $val4){
						$this->addEffect($player,$val4,$kang[$key2],$jisuk[$key2]);
						}
			$this->ct2[$name][$ky]["time"]=time()+$val2[4];
			}
			else{
				$b=$this->ct2[$name][$ky]-time();
				$player->sendMessage($pf."§f남은 쿨타임: ".$b."초");
				}
				}
			}}
			
		else{
			if($cn==$val2[0] && $id==$val2[4] && $dam==$val2[5]){
				$c=0;
				foreach($this->ct[$name] as $key => $val){
					if($val["cn"]==$cn){
						$c=1;
						$nm=$val["cn"];
						$tm=$val["time"];
						$ky=$key;
						}
					}
				if($c==0)array_push($this->ct[$name], array("cn"=>$cn, "time"=>0));
				if($this->ct[$name][$key]["time"]-time()<=0){
					//var_dump($val2);
					if($val2[6]=="앞"){
						$this->dash($player, $val2[2]);
				        $this->ct[$name][$key]["time"]=time()+$val2[3];
						}
					else if($val2[6]=="왼쪽"){
						$this->Ledash($player, $val2[2]);
				        $this->ct[$name][$key]["time"]=time()+$val2[3];
						}
					else if($val2[6]=="오른쪽"){
						$this->Ridash($player, $val2[2]);
				        $this->ct[$name][$key]["time"]=time()+$val2[3];
						}
					else if($val2[6]=="위"){
						$this->Updash($player, $val2[2]);
				        $this->ct[$name][$key]["time"]=time()+$val2[3];
						}
					else if($val2[6]=="뒤"){
						$this->Bcdash($player, $val2[2]);
				        $this->ct[$name][$key]["time"]=time()+$val2[3];
						}
					else{
						
						}
					
					
					/*
					switch($val2[6]){
						case "앞":
						$this->dash($player, $val2[2]);
				        $this->ct[$name]=time()+$val2[3];
				        break;
						case "왼쪽":
						$this->Ledash($player, $val2[2]);
				        $this->ct[$name]=time()+$val2[3];
				        break;
						case "오른쪽":
						$this->Ridash($player, $val2[2]);
				        $this->ct[$name]=time()+$val2[3];
				        break;
						case "뒤":
						
						case "위":
						$this->Updash($player, $val2[2]);
				        $this->ct[$name]=time()+$val2[3];
				        break;
						}*/
				}
				else{
					$b=$this->ct[$name][$key]["time"]-time();
					$player->sendMessage($pf."남은 쿨타임: ".$b."초");
					}
				}
			}



/*
		if($cn==$val2[0] && $id==$val2[4] && $dam==$val2[5]){
			if($val2[1]!="대쉬"){
				if($this->ct2[$name]-time()<=0){
			$this->addEffect($player,$val2[1],$val2[2],$val2[3]);
			$this->ct2[$name]=time()+$val2[4];
			}
			else{
				$player->sendMessage($pf."쿨타임이 아직 남아있습니다.");
				}
			}
			else{
				if($this->ct[$name]-time()<=0){
				$this->dash($player, $val2[2]);
				$this->ct[$name]=time()+$val2[3];
				}
				else{
					$player->sendMessage($pf."쿨타임이 아직 남아있습니다.");
					}
				}
			}*/
		}
		}
		
		public function onHeld(PlayerItemHeldEvent $event){
			$player=$event->getPlayer();
			$name=$player->getName();
			$item=$player->getInventory()->getItemInHand();
        $cn=$item->getCustomName();
        $id=$item->getId();
	    $dam=$item->getDamage();
	foreach($this->db as $key => $val){
		$val2=explode(":",$this->db[$key]["효과"]);
		if($val2[1]!="대쉬"){
		if($cn==$val2[0] && $id==$val2[5] && $dam==$val2[6]){
			$val3=explode("*",$val2[1]);
					$kang=explode("*",$val2[2]);
					$jisuk=explode("*",$val2[3]);
					foreach($val3 as $key2 => $val4){
						$player->removeEffect($val4);
						}
				}
				}
			}
			}
			
			public function save(){
		$this->data->setAll ($this->db);
        $this->data->save ();
        $this->cool->setAll ($this->ct);
        $this->cool->save ();
        $this->cool2->setAll ($this->ct2);
        $this->cool2->save ();
		}
}
