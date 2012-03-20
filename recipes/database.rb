namespace :db do
  require 'inifile'

  desc "Init dump folders" 
  task :init, :roles => :db, :only => { :primary => true } do 
    run "cd #{latest_release} && mkdir app/tmp"
    run "cd #{latest_release} && mkdir app/tmp/dump"
    run "cd #{latest_release} && chmod -R 777 app/tmp"
  end

  desc "Create a dump of db in remote server" 
  task :dump, :roles => :db, :only => { :primary => true } do 
    run "cd #{latest_release} && #{php_bin} #{symfony_console} db:dump --env=#{symfony_env_prod}"
  end

  desc "Download file in local folder" 
  task :download, :roles => :db, :only => { :primary => true } do 
    #param = IniFile::load('app/config/parameters.ini')
    system "rm app/tmp/dump/remote.dbcurrent.sql*"
    run "cp #{latest_release}/app/tmp/dump/current.sql.bz2 #{latest_release}/web/dbcurrent.sql.bz2"
    get "#{latest_release}/web/dbcurrent.sql.bz2", "app/tmp/dump/remote.dbcurrent.sql.bz2"
    run "rm #{latest_release}/web/dbcurrent.sql.bz2"
  end

  namespace :import do

    desc "Import remote db in local production database" 
    task :production, :roles => :db, :only => { :primary => true } do 
        set :local_db_name, "abmundi"
        db.download
        param = IniFile::load('app/config/parameters.ini')['parameters']
        system "bunzip2 -f app/tmp/dump/remote.dbcurrent.sql.bz2"
        system "mysql -u #{param['database_user']} --password=#{param['database_password']} #{local_db_name} < app/tmp/dump/remote.dbcurrent.sql"
        puts "Import complete"
    end

    desc "Import remote db in local testing database" 
    task :testing, :roles => :db, :only => { :primary => true } do 
        set :local_db_name, "abmundi_test"
        db.download
        param = IniFile::load('app/config/parameters.ini')['parameters']
        system "bunzip2 -f app/tmp/dump/remote.dbcurrent.sql.bz2"
        system "mysql -u #{param['database_user']} --password=#{param['database_password']} #{local_db_name} < app/tmp/dump/remote.dbcurrent.sql"
        puts "Import complete"
    end
  end
end