-- phpMyAdmin SQL Dump
-- version 2.8.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 19, 2007 at 01:58 PM
-- Server version: 4.1.20
-- PHP Version: 4.3.9
-- 
-- Database: `cel`
-- 
CREATE DATABASE `cel` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `cel`;

-- --------------------------------------------------------

-- 
-- Table structure for table `algoritmo`
-- 

DROP TABLE IF EXISTS `algoritmo`;
CREATE TABLE `algoritmo` (
  `id_variavel` int(11) NOT NULL auto_increment,
  `nome` varchar(250) NOT NULL default '',
  `id_projeto` varchar(100) NOT NULL default '',
  `valor` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id_variavel`),
  UNIQUE KEY `nome` (`nome`,`id_projeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `algoritmo`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `axioma`
-- 

DROP TABLE IF EXISTS `axioma`;
CREATE TABLE `axioma` (
  `id_axioma` int(11) NOT NULL auto_increment,
  `axioma` varchar(250) NOT NULL default '',
  `id_projeto` int(11) default '30',
  PRIMARY KEY  (`id_axioma`),
  UNIQUE KEY `axioma` (`axioma`,`id_projeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `axioma`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `cenario`
-- 

DROP TABLE IF EXISTS `cenario`;
CREATE TABLE `cenario` (
  `id_cenario` int(11) NOT NULL auto_increment,
  `id_projeto` int(11) NOT NULL default '0',
  `data` date NOT NULL default '0000-00-00',
  `titulo` varchar(255) NOT NULL default '',
  `objetivo` text NOT NULL,
  `contexto` text NOT NULL,
  `atores` text NOT NULL,
  `recursos` text NOT NULL,
  `episodios` text NOT NULL,
  `excecao` text,
  PRIMARY KEY  (`id_cenario`,`data`),
  UNIQUE KEY `IDX_cenario_2` (`id_projeto`,`titulo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `cenario`
-- 



-- --------------------------------------------------------

-- 
-- Table structure for table `centocen`
-- 

DROP TABLE IF EXISTS `centocen`;
CREATE TABLE `centocen` (
  `id_cenario_from` int(11) NOT NULL default '0',
  `id_cenario_to` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_cenario_from`,`id_cenario_to`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `centocen`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `centolex`
-- 

DROP TABLE IF EXISTS `centolex`;
CREATE TABLE `centolex` (
  `id_cenario` int(11) NOT NULL default '0',
  `id_lexico` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_cenario`,`id_lexico`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `centolex`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `conceito`
-- 

DROP TABLE IF EXISTS `conceito`;
CREATE TABLE `conceito` (
  `id_conceito` int(11) NOT NULL auto_increment,
  `nome` varchar(250) NOT NULL default '',
  `descricao` varchar(250) NOT NULL default '',
  `namespace` varchar(250) default NULL,
  `id_projeto` int(11) NOT NULL default '30',
  PRIMARY KEY  (`id_conceito`),
  UNIQUE KEY `nome` (`nome`,`id_projeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `conceito`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `hierarquia`
-- 

DROP TABLE IF EXISTS `hierarquia`;
CREATE TABLE `hierarquia` (
  `id_hierarquia` int(11) NOT NULL auto_increment,
  `id_projeto` int(11) NOT NULL default '0',
  `id_conceito` int(11) NOT NULL default '0',
  `id_subconceito` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_hierarquia`,`id_projeto`,`id_conceito`,`id_subconceito`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `hierarquia`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `impacto`
-- 

DROP TABLE IF EXISTS `impacto`;
CREATE TABLE `impacto` (
  `id_impacto` int(11) NOT NULL auto_increment,
  `id_lexico` int(11) NOT NULL default '0',
  `impacto` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id_impacto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `impacto`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `lexico`
-- 

DROP TABLE IF EXISTS `lexico`;
CREATE TABLE `lexico` (
  `id_lexico` int(11) NOT NULL auto_increment,
  `id_projeto` int(11) NOT NULL default '0',
  `data` date NOT NULL default '0000-00-00',
  `nome` varchar(64) NOT NULL default '',
  `tipo` varchar(15) default NULL,
  `nocao` text NOT NULL,
  `impacto` text,
  PRIMARY KEY  (`id_lexico`,`data`),
  UNIQUE KEY `IDX_lexico_2` (`id_projeto`,`nome`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `lexico`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `lextolex`
-- 

DROP TABLE IF EXISTS `lextolex`;
CREATE TABLE `lextolex` (
  `id_lexico_from` int(11) NOT NULL default '0',
  `id_lexico_to` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_lexico_from`,`id_lexico_to`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `lextolex`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `participa`
-- 

DROP TABLE IF EXISTS `participa`;
CREATE TABLE `participa` (
  `id_usuario` int(11) NOT NULL default '0',
  `id_projeto` int(11) NOT NULL default '0',
  `gerente` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id_usuario`,`id_projeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `participa`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pedidocen`
-- 

DROP TABLE IF EXISTS `pedidocen`;
CREATE TABLE `pedidocen` (
  `id_pedido` int(11) NOT NULL auto_increment,
  `id_usuario` int(11) NOT NULL default '0',
  `id_projeto` int(11) NOT NULL default '0',
  `tipo_pedido` varchar(7) NOT NULL default '',
  `aprovado` int(1) NOT NULL default '0',
  `id_cenario` int(11) default NULL,
  `titulo` varchar(255) NOT NULL default '',
  `objetivo` text NOT NULL,
  `contexto` text NOT NULL,
  `atores` text NOT NULL,
  `recursos` text NOT NULL,
  `episodios` text,
  `justificativa` text,
  `id_status` int(1) default NULL,
  `excecao` text,
  PRIMARY KEY  (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `pedidocen`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `pedidocon`
-- 

DROP TABLE IF EXISTS `pedidocon`;
CREATE TABLE `pedidocon` (
  `id_pedido` int(11) NOT NULL auto_increment,
  `id_usuario` int(11) NOT NULL default '0',
  `id_projeto` int(11) NOT NULL default '0',
  `tipo_pedido` varchar(7) NOT NULL default '',
  `aprovado` int(1) NOT NULL default '0',
  `id_conceito` int(11) default NULL,
  `nome` varchar(255) NOT NULL default '',
  `descricao` text NOT NULL,
  `namespace` text NOT NULL,
  `justificativa` text,
  `id_status` int(1) default NULL,
  PRIMARY KEY  (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `pedidocon`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pedidolex`
-- 

DROP TABLE IF EXISTS `pedidolex`;
CREATE TABLE `pedidolex` (
  `id_pedido` int(11) NOT NULL auto_increment,
  `id_usuario` int(11) NOT NULL default '0',
  `id_projeto` int(11) NOT NULL default '0',
  `tipo_pedido` varchar(7) NOT NULL default '',
  `aprovado` int(1) NOT NULL default '0',
  `id_lexico` int(11) NOT NULL default '0',
  `nome` varchar(64) NOT NULL default '',
  `nocao` text NOT NULL,
  `impacto` text NOT NULL,
  `justificativa` text,
  `id_status` int(1) default NULL,
  `tipo` varchar(20) default NULL,
  PRIMARY KEY  (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `pedidolex`
-- 



-- --------------------------------------------------------

-- 
-- Table structure for table `pedidorel`
-- 

DROP TABLE IF EXISTS `pedidorel`;
CREATE TABLE `pedidorel` (
  `id_pedido` int(11) NOT NULL auto_increment,
  `id_usuario` int(11) NOT NULL default '0',
  `id_projeto` int(11) NOT NULL default '0',
  `tipo_pedido` varchar(7) NOT NULL default '',
  `aprovado` int(1) NOT NULL default '0',
  `id_relacao` int(11) default NULL,
  `nome` varchar(255) NOT NULL default '',
  `justificativa` text,
  `id_status` int(1) default NULL,
  PRIMARY KEY  (`id_pedido`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `pedidorel`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `projeto`
-- 

DROP TABLE IF EXISTS `projeto`;
CREATE TABLE `projeto` (
  `id_projeto` int(11) NOT NULL default '0',
  `nome` varchar(128) NOT NULL default '',
  `data_criacao` date NOT NULL default '0000-00-00',
  `descricao` text NOT NULL,
  `id_status` int(50) default NULL,
  PRIMARY KEY  (`id_projeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `projeto`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `publicacao`
-- 

DROP TABLE IF EXISTS `publicacao`;
CREATE TABLE `publicacao` (
  `id_projeto` int(11) NOT NULL default '0',
  `data_publicacao` date NOT NULL default '0000-00-00',
  `versao` varchar(15) NOT NULL default '',
  `XML` longtext NOT NULL,
  PRIMARY KEY  (`id_projeto`,`versao`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `publicacao`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `relacao`
-- 

DROP TABLE IF EXISTS `relacao`;
CREATE TABLE `relacao` (
  `id_relacao` int(11) NOT NULL auto_increment,
  `nome` varchar(250) NOT NULL default '',
  `id_projeto` int(11) default '30',
  PRIMARY KEY  (`id_relacao`),
  UNIQUE KEY `nome` (`nome`,`id_projeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `relacao`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `relacao_conceito`
-- 

DROP TABLE IF EXISTS `relacao_conceito`;
CREATE TABLE `relacao_conceito` (
  `id_conceito` int(11) NOT NULL default '0',
  `id_relacao` int(11) NOT NULL default '0',
  `predicado` varchar(250) NOT NULL default '',
  `id_projeto` int(11) NOT NULL default '30',
  PRIMARY KEY  (`id_conceito`,`id_relacao`,`predicado`,`id_projeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `relacao_conceito`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `sinonimo`
-- 

DROP TABLE IF EXISTS `sinonimo`;
CREATE TABLE `sinonimo` (
  `id_sinonimo` int(50) NOT NULL auto_increment,
  `id_lexico` int(50) NOT NULL default '0',
  `id_pedidolex` int(50) NOT NULL default '0',
  `nome` varchar(50) NOT NULL default '',
  `id_projeto` int(50) default NULL,
  PRIMARY KEY  (`id_sinonimo`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `sinonimo`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `status`
-- 

DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `Id_status` int(2) NOT NULL auto_increment,
  `Descricao` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`Id_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `status`
-- 

INSERT INTO `status` (`Id_status`, `Descricao`) VALUES (1, 'Pendente'),
(2, 'Aprovado'),
(3, 'Rejeitado'),
(4, 'Publicado'),
(5, 'Privado');

-- --------------------------------------------------------

-- 
-- Table structure for table `usuario`
-- 

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL auto_increment,
  `nome` varchar(255) NOT NULL default '',
  `email` varchar(64) NOT NULL default '',
  `login` varchar(32) NOT NULL default '',
  `senha` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id_usuario`),
  UNIQUE KEY `IDX_usuario_2` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `usuario`
-- 

INSERT INTO `usuario` (`id_usuario`, `nome`, `email`, `login`, `senha`) VALUES (1, 'user', 'user@teste.com.br', 'user', '1234');