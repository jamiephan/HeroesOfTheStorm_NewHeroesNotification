@echo off


for /r %~dp0 %%G in (.New*.bat) do start "" "%%~G"
