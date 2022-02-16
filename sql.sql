CREATE TABLE `tarefas` ( 
  `id` INT AUTO_INCREMENT NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descricao` VARCHAR(500) NOT NULL,
  `concluida` TINYINT(1) NOT NULL,
   PRIMARY KEY (`id`)
);