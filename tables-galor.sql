CREATE TABLE `card` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL
)

INSERT INTO `card` (`id`, `name`) VALUES
(16, 'Adreus, Keeper of Armageddon'),
(21, 'Alexandrite Dragon'),
(24, 'Ally of Justice Garadholg'),
(3, 'Aqua Chorus'),
(6, 'Banner of Courage'),
(12, 'Black Luster Ritual'),
(13, 'Black Luster Soldier'),
(15, 'Blue-Eyes Ultimate Dragon'),
(19, 'Blue-Eyes White Dragon'),
(7, 'Broken Bamboo Sword'),
(10, 'Burial From a Different Dimension'),
(9, 'Chorus of Sanctuary'),
(1, 'Compulsory Evacuation Device'),
(4, 'Dark Hole'),
(14, 'Dragon Master Knight'),
(23, 'Flamvell Guard'),
(25, 'Gandora the Dragon of Destruction'),
(2, 'Magic Jammer'),
(17, 'Number 15: Gimmick Puppet Giant Grinder'),
(18, 'Number C15: Gimmick Puppet Giant Hunter'),
(5, 'Polymerization'),
(20, 'Rabidragon'),
(11, 'Rank-Up-Magic Quick Chaos'),
(8, 'Temple of the Minds Eye'),
(22, 'The White Stone of Legend');

CREATE TABLE `decks` (
  `deck_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) 

INSERT INTO `decks` (`deck_id`, `user_id`, `cards`) VALUES
(1, 1, '{\"Blue Eyes White Dragon\":{\"name\":\"Blue Eyes White Dragon\",\"type\":\"Monster\",\"quantity\":3},\"Polymerization\":{\"name\":\"Polymerization\",\"type\":\"Spell\",\"quantity\":1},\"Blue Eyes Ultimate Dragon\":{\"name\":\"Blue Eyes Ultimate Dragon\",\"type\":\"Monster\",\"quantity\":1}}');

CREATE TABLE `fusings` (
  `hidden_id` int(11) NOT NULL,
  `fusionSpell` varchar(60) NOT NULL,
  `fusionMonster` varchar(60) NOT NULL
) 

INSERT INTO `fusings` (`hidden_id`, `fusionSpell`, `fusionMonster`) VALUES
(1, 'Polymerization', 'Blue Eyes Ultimate Dragon'),
(2, 'Polymerization', 'Dragon Master Knight');

CREATE TABLE `fusionMon` (
  `hidden_id` int(11) NOT NULL,
  `fusionMonster` varchar(60) NOT NULL,
  `fusionMaterial` varchar(60) NOT NULL,
  `quantity` int(11) DEFAULT 1
) 

INSERT INTO `fusionMon` (`hidden_id`, `fusionMonster`, `fusionMaterial`, `quantity`) VALUES
(1, 'Blue Eyes Ultimate Dragon', 'Blue Eyes White Dragon', 3),
(2, 'Dragon Master Knight', 'Blue Eyes Ultimate Dragon', 1),
(3, 'Dragon Master Knight', 'Black Luster Soldier', 1);

CREATE TABLE `monster` (
  `card_name` varchar(60) NOT NULL,
  `subtype` varchar(60) NOT NULL,
  `attribute` varchar(60) NOT NULL,
  `effect_type` varchar(60) DEFAULT NULL,
  `atk` int(11) DEFAULT 0,
  `def` int(11) DEFAULT 0,
  `level` int(11) DEFAULT 1
)

INSERT INTO `monster` (`card_name`, `subtype`, `attribute`, `effect_type`, `atk`, `def`, `level`) VALUES
('Adreus Keeper of Armageddon', 'Fiend', 'Dark', 'Effect', 2600, 1700, 5),
('Alexandrite Dragon', 'Dragon', 'Light', NULL, 2000, 100, 4),
('Ally of Justice Garadholg', 'Machine', 'Dark', 'Effect', 1600, 400, 4),
('Black Luster Soldier', 'Warrior', 'Earth', NULL, 3000, 2500, 8),
('Blue Eyes Ultimate Dragon', 'Dragon', 'Light', NULL, 4500, 3800, 12),
('Blue Eyes White Dragon', 'Dragon', 'Light', NULL, 3000, 2500, 8),
('Dragon Master Knight', 'Dragon', 'Light', 'Effect', 5000, 5000, 12),
('Flamvell Guard', 'Dragon', 'Fire', NULL, 100, 2000, 1),
('Gandora the Dragon of Destruction', 'Dragon', 'Dark', 'Effect', 0, 0, 8),
('Number 15 Gimmick Puppet Giant Grinder', 'Machine', 'Dark', 'Effect', 1500, 2500, 8),
('Number C15 Gimmick Puppet Giant Hunter', 'Machine', 'Dark', 'Effect', 2500, 1500, 9),
('Rabidragon', 'Dragon', 'Light', NULL, 2950, 2900, 8),
('The White Stone of Legend', 'Dragon', 'Light', 'Effect', 300, 250, 1);

CREATE TABLE `rankUp` (
  `hidden_id` int(11) NOT NULL,
  `xyz` varchar(60) NOT NULL,
  `cxyz` varchar(60) NOT NULL,
  `rankUpSpell` varchar(60) NOT NULL
) 

INSERT INTO `rankUp` (`hidden_id`, `xyz`, `cxyz`, `rankUpSpell`) VALUES
(1, 'Number 15 Gimmick Puppet Giant Grinder', 'Number C15 Gimmick Puppet Giant Hunter', 'Rank Up Magic Quick Chaos');

CREATE TABLE `rituals` (
  `ritualSpell` varchar(60) NOT NULL,
  `ritualMonster` varchar(60) NOT NULL
) 

INSERT INTO `rituals` (`ritualSpell`, `ritualMonster`) VALUES
('Black Luster Ritual', 'Black Luster Soldier');

CREATE TABLE `spell` (
  `card_name` varchar(60) NOT NULL,
  `subtype` varchar(60) NOT NULL
) 

INSERT INTO `spell` (`card_name`, `subtype`) VALUES
('Banner of Courage', 'Continuous'),
('Black Luster Ritual', 'Ritual'),
('Broken Bamboo Sword', 'Equip'),
('Burial From a Different Dimension', 'Quick Play'),
('Chorus of Sanctuary', 'Field'),
('Dark Hole', 'Normal'),
('Polymerization', 'Normal'),
('Rank Up Magic Quick Chaos', 'Quick Play');

CREATE TABLE `trap` (
  `card_name` varchar(60) NOT NULL,
  `subtype` varchar(60) NOT NULL
) 

INSERT INTO `trap` (`card_name`, `subtype`) VALUES
('Aqua Chorus', 'Continuous '),
('Compulsory Evacuation Device', 'Normal'),
('Magic Jammer', 'Counter');

CREATE TABLE `yugiohusers` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(60) NOT NULL
) 

INSERT INTO `yugiohusers` (`id`, `username`, `password`) VALUES
(1, 'user', '123'),
(2, 'user2', '12345');

ALTER TABLE `card`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `decks`
  ADD PRIMARY KEY (`deck_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

ALTER TABLE `fusings`
  ADD PRIMARY KEY (`hidden_id`);

ALTER TABLE `fusionMon`
  ADD PRIMARY KEY (`hidden_id`);

ALTER TABLE `monster`
  ADD PRIMARY KEY (`card_name`);

ALTER TABLE `rankUp`
  ADD PRIMARY KEY (`hidden_id`),
  ADD KEY `cxyz` (`cxyz`),
  ADD KEY `xyz` (`xyz`),
  ADD KEY `rankUpSpell` (`rankUpSpell`);

ALTER TABLE `rituals`
  ADD PRIMARY KEY (`ritualSpell`),
  ADD UNIQUE KEY `ritualMonster` (`ritualMonster`);

ALTER TABLE `spell`
  ADD PRIMARY KEY (`card_name`);

ALTER TABLE `trap`
  ADD PRIMARY KEY (`card_name`);

ALTER TABLE `yugiohusers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `decks`
  MODIFY `deck_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `fusings`
  MODIFY `hidden_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `fusionMon`
  MODIFY `hidden_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `rankUp`
  MODIFY `hidden_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `yugiohusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `decks`
  ADD CONSTRAINT `users` FOREIGN KEY (`user_id`) REFERENCES `yugiohusers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `rankUp`
  ADD CONSTRAINT `rankUp_ibfk_1` FOREIGN KEY (`cxyz`) REFERENCES `monster` (`card_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rankUp_ibfk_2` FOREIGN KEY (`xyz`) REFERENCES `monster` (`card_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rankUp_ibfk_3` FOREIGN KEY (`rankUpSpell`) REFERENCES `spell` (`card_name`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
