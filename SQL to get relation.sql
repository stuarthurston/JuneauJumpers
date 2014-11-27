Select E.*, M.*
From tblEvents as E
    Left join tblAttends as A on E.pmkEventId = A.fnkEventId
    left join tblMembers as M on A.fnkMemberId = M.pmkMemberId
where E.pmkEventId = '37';


Select M.pmkMemberId
From tblEvents as E
    Left join tblAttends as A on E.pmkEventId = A.fnkEventId
    left join tblMembers as M on A.fnkMemberId = M.pmkMemberId
where E.pmkEventId = '37';




Select E.*, M.*
From tblMembers as M
    Left join tblAttends as A on M.pmkMemberId = A.fnkMemberId
    left join tblEvents as E on A.fnkEventId = E.pmkEventId
where M.pmkMemberId = '7';