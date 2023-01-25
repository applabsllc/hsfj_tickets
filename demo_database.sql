

CREATE TABLE IF NOT EXISTS `tickets_adm` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(220) NOT NULL,
  `title` varchar(220) NOT NULL,
  `depto` varchar(220) NOT NULL,
  `status` int(10) unsigned default '0',
  `ts_created` timestamp NULL default CURRENT_TIMESTAMP,
  `estimate` int(10) unsigned default '0',
  `progress` int(10) unsigned default '0',
  `priority` int(11) default '0',
  `owner` varchar(220) NOT NULL,
  `assigned` varchar(220) NOT NULL default '0',
  `completed` int(10) unsigned default '0',
  `ts_completed` timestamp NOT NULL default '0000-00-00 00:00:00',
  `ts_planeacion` timestamp NULL default NULL,
  `ts_progreso` timestamp NULL default NULL,
  `ts_revision` timestamp NULL default NULL,
  `deleted` int(10) unsigned default '0',
  `filesrc` longtext NOT NULL,
  `desc` longtext NOT NULL,
  `notes` longtext NOT NULL,
  `estimatecost` int(10) unsigned default NULL,
  `finalcost` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;



INSERT INTO `tickets_adm` (`id`, `type`, `title`, `depto`, `status`, `ts_created`, `estimate`, `progress`, `priority`, `owner`, `assigned`, `completed`, `ts_completed`, `ts_planeacion`, `ts_progreso`, `ts_revision`, `deleted`, `filesrc`, `desc`, `notes`, `estimatecost`, `finalcost`) VALUES
(1, 'feature', 'f5t4t45g4', 'Prueba depto 2', 3, '2022-10-24 14:35:41', 1, 15, 0, 'admin', 'Trabajador 1', 0, '0000-00-00 00:00:00', '2022-12-28 19:38:01', '2022-12-28 19:38:01', NULL, 0, '[]', '', '[]', NULL, NULL),
(2, 'feature', 'the thing', '', 0, '2022-10-31 15:53:18', 0, 0, 0, 'admin', 'Proveedor Externo', 0, '0000-00-00 00:00:00', NULL, NULL, NULL, 0, '[{"src":"tickets_adm/10199_store.png","name":"store.png"},{"src":"tickets_adm/10803_kingsandgenerals.txt","name":"kings and generals.txt"}]', '', '[{"note":"eeww","files":[]},{"note":"we wedwed","files":[]},{"note":"qwee wqe we weqew","files":[{"src":"tickets_adm/9610_store.png","name":"store.png"}]},{"note":"wewedwedwed","files":[]},{"note":"e rer ferfer","files":[]},{"note":" werererwer werewrr","files":[]},{"note":"wer we rer er wer","files":[]},{"note":" wer rwerwerwe","files":[]},{"note":" qererer","files":[]},{"note":" qererer","files":[]},{"note":"dfvdfvsdfv","ts":null,"files":[]},{"note":"wedfwef","ts":"2022/11/02","files":[]},{"note":"sdfvdsf df df","ts":"2022/11/02 20:51:15","files":[]},{"note":"dfvsdfvsdfv","ts":"admin - 2022/11/02 20:18:16","files":[]},{"note":"wwwwwwwwwwwww","ts":"admin - 2022/11/02 20:30:25","files":[]},{"note":"wweedwe wewe","ts":"admin - 2022/11/02 20:39:52","files":[{"src":"tickets_adm/14451_1024px-OXXO_logo.svg.png","name":"1024px-OXXO_logo.svg.png"}]},{"note":"esto es una nota larga muy largajkh djkhsk kjsh dkj hsdjkh sdjskjdjkksd jksd hsdjsdjs djs dsd hsdhsdjhs djs djsdjs djsdd","ts":"admin - 2022/11/02 20:58:53","files":[{"src":"tickets_adm/6067_store.png","name":"store.png"}]}]', 0, 0),
(3, 'feature', 'Test', '', 0, '2022-12-28 18:48:17', 0, 0, 0, 'admin', '0', 0, '0000-00-00 00:00:00', NULL, '2022-12-28 18:48:17', NULL, 0, '[]', '', '', 0, 0);


CREATE TABLE IF NOT EXISTS `tickets_adm_deptos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nombre` varchar(210) NOT NULL,
  `padre` int(10) unsigned NOT NULL default '0',
  `encargado` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `tickets_adm_deptos` (`id`, `nombre`, `padre`, `encargado`) VALUES
(1, 'Piso 1', 0, 0),
(2, 'Piso 2', 0, 0);