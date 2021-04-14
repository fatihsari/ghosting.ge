@echo off
if "%~1"=="push" (
	SET comment=%~2
	if "%comment%"=="" (
		SET comment=Update
	)
	git config --global user.email "n5g11@yahoo.com"
	git config --global user.name "n5g11"
	git add -A
	git commit -m "[%comment%]"
	git push origin master
) ELSE IF "%~1"=="pull" (
   git pull origin master
) ELSE (
    echo "Usage : "
	echo "         PUSH - gt.bat push \"comment\""
	echo "         PULL - gt.bat pull"
)
