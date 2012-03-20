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
    run "cp #{latest_release}/app/tmp/dump/current.sql.bz2 #{latest_release}/web/dbcurrent.sql.bz2"
  end
end