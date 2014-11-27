CREATE TABLE IF NOT EXISTS tblAttends (
    fnkMemberId int(10) not null,
    fnkEventId int(10) not null,
    PRIMARY KEY (fnkMemberId, fnkEventId),
    FOREIGN KEY (fnkMemberId) REFERENCES tblMembers (pmkMemberId)
       ON DELETE CASCADE
       ON UPDATE CASCADE,
    FOREIGN KEY (fnkEventId) REFERENCES tblEvents (pmkEventId)
       ON DELETE CASCADE
       ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;