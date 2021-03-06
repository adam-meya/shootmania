<?php

namespace ManiaLivePlugins\MLEPP\AddRemoveMaps\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Bgs1InRace;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Elements\BgsPlayerCard;
use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLive\Utilities\Time;
use ManiaLive\DedicatedApi\Connection;
use ManiaLive\Gui\Controls\Frame;
use ManiaLive\Gui\Controls\PageNavigator;

class AddLocalWindow extends \ManiaLive\Gui\ManagedWindow
{	
	private $tableau = array();
	private $navigator;
	private $bgresume;
	private $textInfos;
	private $showInfos = false;
	
	private $page;
	private $nbpage;
	private $entry;

	private $action;
	
	function onConstruct()
	{
		parent::onConstruct();
		$this->setSize(180, 120);
		$this->centerOnScreen();
		$this->tableau = new Frame();
		$this->tableau->setPosition(0, -10);
		$this->addComponent($this->tableau);
		
		$this->navigator = new PageNavigator();
		//$this->navigator->setIconSize(6);
		$this->addComponent($this->navigator);
		
		/*$this->bgresume = new Bgs1InRace();
		$this->bgresume->setSubStyle(Bgs1InRace::BgList);
		$this->bgresume->setSize(81, 4);
		$this->bgresume->setPosition(2, 5.5, 2);
		$this->addComponent($this->bgresume);*/
		
		$this->makeFirstLine(-15.5);
		$this->page = 1;
		$this->nbpage = 1;
	}
	
	function setInfos($entry = array())
	{
		$this->entry = $entry;
	}
	
	function makeFirstLine($posy = 0)
	{
		$texte = new Label();
		$texte->setSize(40, 4);
		$texte->setPosition(20, $posy, 2);
		$texte->setTextColor("000");
		$texte->setTextSize(2);
		$texte->setText("\$oFilename");
		$this->addComponent($texte);
	}
	
	function onDraw()
	{
		$this->tableau->clearComponents();
		$posy = 0;
		$num = 1;
		$this->setTitle('Adding Tracks');
			$posy -= 10;
			for($i=($this->page-1)*15; $i<=($this->page)*15-1; ++$i)
			{
				$this->setLineBgs($posy, $i, $this->entry);
				$texte = new Label();
				$texte->setSize(64.5, 3);
				$texte->setPosition(50, $posy-0.5, 2);
				$texte->setTextColor("000");
				$texte->setTextSize(2);
				$texte->setHalign("right");
				$texte->setText('$fff'.$this->entry['Filename'][0]);
				$this->tableau->addComponent($texte);
				$texte = new Label();
				$texte->setSize(63, 3);
				$texte->setPosition(15.5, $posy-0.5, 3);
				$texte->setTextColor("000");
				$texte->setTextSize(2);
				$posy -= 6;
			}
		
		$this->nbpage = intval((count($this->entry)-1)/15)+1;
		
		$this->navigator->setPositionX($this->getSizeX() / 2);
		$this->navigator->setPositionY(-($this->getSizeY() - 6));
		$this->navigator->setCurrentPage($this->page);
		$this->navigator->setPageNumber($this->nbpage);
		$this->navigator->showText(true);
		$this->navigator->showLast(true);

		if ($this->page < $this->nbpage)
		{
			$this->navigator->arrowNext->setAction($this->createAction(array($this,'showNextPage')));
			$this->navigator->arrowLast->setAction($this->createAction(array($this,'showLastPage')));
		}
		else
		{
			$this->navigator->arrowNext->setAction(null);
			$this->navigator->arrowLast->setAction(null);
		}

		if ($this->page > 1)
		{
			$this->navigator->arrowPrev->setAction($this->createAction(array($this,'showPrevPage')));
			$this->navigator->arrowFirst->setAction($this->createAction(array($this,'showFirstPage')));
		}
		else
		{
			$this->navigator->arrowPrev->setAction(null);
			$this->navigator->arrowFirst->setAction(null);
		}
	}
	
	function showPrevPage($login = null)
	{
		$this->page--;
		if ($login) $this->show();
	}

	function showNextPage($login = null)
	{
		$this->page++;
		if ($login) $this->show();
	}

	function showLastPage($login = null)
	{
		$this->page = $this->nbpage;
		if ($login) $this->show();
	}

	function showFirstPage($login = null)
	{
		$this->page = 1;
		if ($login) $this->show();
	}
	
	function showInfos($login = null, $showInfos)
	{
		$this->showInfos = $showInfos;
		if($login)$this->show();
	}
	
	function setAction($action = array())
	{
		$this->action = $action;
	}
	
	function setLineBgs($posy, $i, $entry)
	{
	if($i == $entry)
		{
			$bg = new Bgs1InRace();
			$bg->setSubStyle(Bgs1InRace::NavButtonBlink);
			$bg->setPosition(14.5, $posy+1, 2);
			$bg->setSize(64.5,6);
			$this->tableau->addComponent($bg);
		}
	else if(count($entry) - $i > 1)
		{
			$bg = new BgsPlayerCard();
			$bg->setSubStyle(BgsPlayerCard::BgCardSystem);
			$bg->setAction($this->createAction(array($this, 'imposeChallenge'), $i, $entry));
			$bg->setPosition(14.5, $posy+0.5, 2);
			$bg->setSize(64.5,5);
			$this->tableau->addComponent($bg);
		}
	}
	
	function imposeChallenge($login, $i, $entry)
	{
		call_user_func($this->action, $login, $i, $entry['Filename'][1][0]);
		$this->hide();
	}
}
?>