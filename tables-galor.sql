 CREATE TABLE card (
  id INT NOT NULL PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE
);
 
CREATE TABLE monster (
  card_name VARCHAR(60) NOT NULL PRIMARY KEY REFERENCES card(name),
  subtype VARCHAR(60) NOT NULL,
  attribute VARCHAR(60) NOT NULL,    
  effect_type VARCHAR(60) DEFAULT NULL,
  atk INT DEFAULT '0',
  def INT DEFAULT '0',
  level INT DEFAULT '1'  
);

CREATE TABLE spell (
  card_name VARCHAR(60) NOT NULL PRIMARY KEY REFERENCES card(name),
  subtype VARCHAR(60) NOT NULL
);

CREATE TABLE trap (
  card_name VARCHAR(60) NOT NULL PRIMARY KEY REFERENCES card(name),
  subtype VARCHAR(60) NOT NULL
);

CREATE TABLE rankUp (
  hidden_id INT AUTO_INCREMENT PRIMARY KEY,
  cXYZ VARCHAR(60) NOT NULL REFERENCES monster(card_name),
  rankUpSpell VARCHAR(60) NOT NULL REFERENCES spell(card_name)
);

CREATE TABLE fusings (
  hidden_id INT AUTO_INCREMENT PRIMARY KEY,
  fusionSpell VARCHAR(60) NOT NULL REFERENCES spell(card_name),
  fusionMonster VARCHAR(60) NOT NULL REFERENCES fusionMon(fusionMonster)
);

CREATE TABLE fusionMon (
  hidden_id INT AUTO_INCREMENT PRIMARY KEY,
  fusionMonster VARCHAR(60) NOT NULL REFERENCES monster(card_name),
  fusionMaterial VARCHAR(60) NOT NULL REFERENCES monster(card_name),
  quantity INT DEFAULT '1'
);

CREATE TABLE rituals (
  ritualSpell VARCHAR(60) NOT NULL PRIMARY KEY REFERENCES spell(card_name),
  ritualMonster VARCHAR(60) NOT NULL UNIQUE REFERENCES monster(card_name)
);

CREATE TABLE decks (
  deck_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL REFERENCES yugiohusers(id),
  cards longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
);

CREATE TABLE yugiohusers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL,
  password VARCHAR(60) NOT NULL
);

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

INSERT INTO `fusings` (`hidden_id`, `fusionSpell`, `fusionMonster`) VALUES
(1, 'Polymerization', 'Blue Eyes Ultimate Dragon'),
(2, 'Polymerization', 'Dragon Master Knight');

INSERT INTO `fusionMon` (`hidden_id`, `fusionMonster`, `fusionMaterial`, `quantity`) VALUES
(1, 'Blue Eyes Ultimate Dragon', 'Blue Eyes White Dragon', 3),
(2, 'Dragon Master Knight', 'Blue Eyes Ultimate Dragon', 1),
(3, 'Dragon Master Knight', 'Black Luster Soldier', 1);

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

INSERT INTO `rankUp` (`cXYZ`, `rankUpSpell`) VALUES
('Number C15 Gimmick Puppet Giant Hunter', 'Rank Up Magic Quick Chaos');

INSERT INTO `rituals` (`ritualSpell`, `ritualMonster`) VALUES
('Black Luster Ritual', 'Black Luster Soldier');

INSERT INTO `spell` (`card_name`, `subtype`) VALUES
('Banner of Courage', 'Continuous'),
('Black Luster Ritual', 'Ritual'),
('Broken Bamboo Sword', 'Equip'),
('Burial From a Different Dimension', 'Quick Play'),
('Chorus of Sanctuary', 'Field'),
('Dark Hole', 'Normal'),
('Polymerization', 'Normal'),
('Rank Up Magic Quick Chaos', 'Quick Play');

INSERT INTO `trap` (`card_name`, `subtype`) VALUES
('Aqua Chorus', 'Continuous '),
('Compulsory Evacuation Device', 'Normal'),
('Magic Jammer', 'Counter');

INSERT INTO `yugiohusers` (`id`, `username`, `password`) VALUES
(1, 'user', '123'),
(2, 'user2', '12345');