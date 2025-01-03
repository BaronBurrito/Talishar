<?php

  function MONBrutePlayAbility($cardID, $from, $resourcesPaid, $target, $additionalCosts)
  {
    global $currentPlayer;
    $rv = "";
    switch($cardID)
    {
      case "MON121":
        $numBD = SearchCount(SearchBanish($currentPlayer, "", "", -1, -1, "", "", true));
        $damage = 6 - $numBD;
        WriteLog("Player " . $currentPlayer . " lost " . $damage . " life");
        DamageTrigger($currentPlayer, $damage, "PLAYCARD", $cardID);
        return "";
      case "MON125":
        Draw($currentPlayer);
        $card = DiscardRandom();
        if(ModifiedAttackValue($card, $currentPlayer, "HAND", source:$cardID) >= 6) {
          MZMoveCard($currentPlayer, "MYDECK:bloodDebtOnly=true", "MYBANISH,DECK,-", may:true);
          AddDecisionQueue("SHUFFLEDECK", $currentPlayer, "-");
        }
        return "";
      case "MON138": case "MON139": case "MON140":
        Draw($currentPlayer);
        $card = DiscardRandom();
        if(ModifiedAttackValue($card, $currentPlayer, "HAND", source:$cardID) >= 6) {
          AddDecisionQueue("MULTIZONEINDICES", $currentPlayer, "MYDISCARD&THEIRDISCARD");
          AddDecisionQueue("SETDQCONTEXT", $currentPlayer, "Choose a card to banish with " . CardLink($cardID, $cardID), 1);
          AddDecisionQueue("CHOOSEMULTIZONE", $currentPlayer, "<-", 1);
          AddDecisionQueue("MZBANISH", $currentPlayer, "GY,-," . $currentPlayer, 1);
          AddDecisionQueue("MZREMOVE", $currentPlayer, "-", 1);
        }
        return "";
      case "MON150": case "MON151": case "MON152":
        AddCurrentTurnEffect($cardID, $currentPlayer);
        return "";
      case "MON221":
        Draw($currentPlayer);
        $card = DiscardRandom();
        if(ModifiedAttackValue($card, $currentPlayer, "HAND", source:$cardID) >= 6) AddCurrentTurnEffect($cardID, $currentPlayer);
        return "";
      case "MON222":
        Draw($currentPlayer);
        $card = DiscardRandom();
        if(ModifiedAttackValue($card, $currentPlayer, "HAND", source:$cardID) >= 6) AddCurrentTurnEffect($cardID, $currentPlayer);
        return "";
      case "MON223": case "MON224": case "MON225":
        Draw($currentPlayer);
        $card = DiscardRandom();
        if(ModifiedAttackValue($card, $currentPlayer, "HAND", source:$cardID) >= 6) AddCurrentTurnEffect($cardID, $currentPlayer);
        return "";
      default: return "";
    }
  }

  function MONBruteHitEffect($cardID)
  {
    switch($cardID)
    {
      default: break;
    }
  }

  function RandomBanish3GY($cardID, $modifier = "NA")
  {
    global $currentPlayer;
    $hand = &GetHand($currentPlayer);
    $discard = &GetDiscard($currentPlayer);
    if(count($discard) < 3) return;
    $BanishedIncludes6 = 0;
    $diabolicOfferingCount = 0;
    for($i = 0; $i < 3; $i++) {
      $index = GetRandom(0, count($discard)/DiscardPieces()-1) * DiscardPieces();
      if(ModifiedAttackValue($discard[$index], $currentPlayer, "GY", source:$cardID) >= 6) ++$BanishedIncludes6;
      elseif($discard[$index] == "DTD107") ++$diabolicOfferingCount;
      $cardID = RemoveDiscard($currentPlayer, $index);
      BanishCardForPlayer($cardID, $currentPlayer, "DISCARD", $modifier);
      $discard = array_values($discard);
    }
    if($BanishedIncludes6 > 0) $BanishedIncludes6 += $diabolicOfferingCount;
    return $BanishedIncludes6 > 3 ? 3 : $BanishedIncludes6;
  }

  function LadyBarthimontAbility($player, $index)
  {
    $deck = new Deck($player);
    if($deck->Empty()) return;
    $topDeck = $deck->BanishTop("-", $player);
    if(ModifiedAttackValue($topDeck, $player, "DECK", source:"MON406") >= 6) {
      $arsenal = &GetArsenal($player);
      ++$arsenal[$index+3];
      AddCurrentTurnEffect("MON406", $player);
      if($arsenal[$index+3] == 2) MentorTrigger($player, $index);
    }
  }

?>
