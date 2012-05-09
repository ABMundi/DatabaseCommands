namespace :maintenance do
    task :enable, :roles => :web, :except => { :no_release => true } do
        on_rollback { 
        run "cp -f #{current_path}/#{web_path}/_.htaccess #{current_path}/#{web_path}/.htaccess"
        run "rm -f #{current_path}/#{app_path}/web/#{maintenance_basename}.html" 
        }

        run "cp -f #{current_path}/#{system_path}/#{maintenance_basename}.html #{current_path}/#{web_path}/#{maintenance_basename}.html"
        run "cp -f #{current_path}/#{web_path}/.htaccess #{current_path}/#{web_path}/_.htaccess"
        run "cp -f #{current_path}/#{system_path}/.htaccess #{current_path}/#{web_path}/.htaccess"
    end

    task :disable, :roles => :web, :except => { :no_release => true } do
        run "cp -f #{current_path}/#{web_path}/_.htaccess #{current_path}/#{web_path}/.htaccess"
        run "rm -f #{current_path}/#{shared_path}/web/#{maintenance_basename}.html" 
    end
end