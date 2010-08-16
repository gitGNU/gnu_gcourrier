ALTER TABLE courrier
  ADD INDEX (`validite`),
  ADD INDEX (`type`),
  ADD INDEX (`serviceCourant`),
  ADD INDEX (`idDestinataire`);
ALTER TABLE estTransmis
  ADD INDEX (`idCourrier`),
  ADD INDEX (`idService`);

ALTER TABLE facture
  ADD INDEX (`validite`),
  ADD INDEX (`idServiceCreation`),
  ADD INDEX (`idFournisseur`);
ALTER TABLE estTransmisCopie
  ADD INDEX (`idFacture`),
  ADD INDEX (`idService`);
