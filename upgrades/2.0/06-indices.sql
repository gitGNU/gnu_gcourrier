ALTER TABLE courrier
  ADD INDEX `idx_validite`       (`validite`),
  ADD INDEX `idx_type`           (`type`),
  ADD INDEX `idx_serviceCourant` (`serviceCourant`),
  ADD INDEX `idx_idDestinataire` (`idDestinataire`);
ALTER TABLE estTransmis
  ADD INDEX `idx_idCourrier` (`idCourrier`),
  ADD INDEX `idx_idService`  (`idService`);

ALTER TABLE facture
  ADD INDEX `idx_validite`          (`validite`),
  ADD INDEX `idx_idServiceCreation` (`idServiceCreation`),
  ADD INDEX `idx_idFournisseur`     (`idFournisseur`);
ALTER TABLE estTransmisCopie
  ADD INDEX `idx_idFacture` (`idFacture`),
  ADD INDEX `idx_idService` (`idService`);
