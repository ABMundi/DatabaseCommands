namespace :db do
  require 'inifile'
  desc "Backup the remote production database" 
  task :backup, :roles => :db, :only => { :primary => true } do 
    filename = "#{Time.now.to_i}.sql.bz2" 
    file = "app/tmp/#{filename}" 
    param = IniFile::load('app/config/parameters.ini')
    
    puts param['parameters']['database_name']

    #run "cd #{latest_release} && mysqldump -u #{db['username']} --password=#{db['password']} #{db['database']} | bzip2 -c > #{file}" do |ch, stream, data| 
    #  puts data 
    #end 
    #run "mkdir -p #{File.dirname(__FILE__)}/../backups/"
    #get file, "backups/#{filename}" 
  end
end