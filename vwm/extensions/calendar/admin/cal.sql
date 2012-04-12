CREATE TABLE calendar_admins (
  admin_id int(11) NOT NULL auto_increment,
  login varchar(30) NOT NULL default '',
  paswoord varchar(30) NOT NULL default '',
  PRIMARY KEY  (admin_id)
) TYPE=MyISAM;

INSERT INTO calendar_admins VALUES (1,'god','ad2ZB6vABn6A6');

CREATE TABLE calendar_cat (
  cat_id int(11) NOT NULL auto_increment,
  cat_name varchar(150) NOT NULL default '',
  PRIMARY KEY  (cat_id),
  UNIQUE KEY cat_id (cat_id)
) TYPE=MyISAM;

INSERT INTO calendar_cat VALUES (1,'general');

CREATE TABLE calendar_events (
  id int(11) NOT NULL auto_increment,
  title varchar(255) NOT NULL default '',
  description text NOT NULL,
  url varchar(100) NOT NULL default '',
  email varchar(120) NOT NULL default '',
  cat tinyint(2) NOT NULL default '0',
  day tinyint(2) NOT NULL default '0',
  month smallint(2) NOT NULL default '0',
  year smallint(4) NOT NULL default '0',
  approved tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;




