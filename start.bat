for /f "tokens=4 delims= " %%i in ('route print ^| find " 0.0.0.0"') do set localip=%%i
start firefox.exe %localip%:8000
python -m http.server 8000
pause