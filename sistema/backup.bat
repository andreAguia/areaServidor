echo off
REM Rotina de Backup do Mysql
REM Desenvolvidor: Alat
REM Atualizado em 18/06/2015

REM Indo para o diretório do Mysql fornecido pelo sistema (%1)
cd %1

REM formata hora
set ftime=%time:~0,2%
set ftime=0%ftime: =%
set ftime=%ftime:~-2%

SET path=%path%;c:\Program Files\Java\jdk1.X.X.X_XX\bin 

REM Executa o mysqldump para os bancos de dados grh e areaServidor
mysqldump -h localhost -u root -pDSvuEtwz6h9HfLCF grh > C:/_backup/%date:~6,10%.%date:~3,2%.%date:~0,2%_%ftime%.%time:~3,2%.grh.sql
mysqldump -h localhost -u root -pDSvuEtwz6h9HfLCF areaServidor > C:/_backup/%date:~6,10%.%date:~3,2%.%date:~0,2%_%ftime%.%time:~3,2%.areaServidor.sql

REM compacta arquivos
SET PATH=%PATH%;C:\Program Files\7-Zip
7z a -tzip C:\_backup\%date:~6,10%.%date:~3,2%.%date:~0,2%_%ftime%.%time:~3,2%.zip C:\_backup\*.sql

REM Apaga os arquivos de origem
del C:\_backup\*.sql

REM Verifica e cria a pasta do ano
IF NOT EXIST C:\_backup\%date:~6,10%\ (
	md C:\_backup\%date:~6,10%\
) 

REM Verifica e cria a pasta do mes
IF NOT EXIST C:\_backup\%date:~6,10%\%date:~3,2%\ (
	md C:\_backup\%date:~6,10%\%date:~3,2%\
)

REM Verifica e cria a pasta do ano no google drive
IF NOT EXIST C:\Users\gti\"Google Drive"\backup\%date:~6,10%\ (
	md C:\Users\gti\"Google Drive"\backup\%date:~6,10%\
)

REM Verifica e cria a pasta do mes no google drive
IF NOT EXIST C:\Users\gti\"Google Drive"\backup\%date:~6,10%\%date:~3,2%\ (
	md C:\Users\gti\"Google Drive"\backup\%date:~6,10%\%date:~3,2%\
)

REM Copia os arquivos do dia para o google drive
copy C:\_backup\%date:~6,10%.%date:~3,2%.%date:~0,2%*.* C:\Users\gti\"Google Drive"\backup\%date:~6,10%\%date:~3,2%\

REM Move os arquivos para a pasta do mês especifica
move C:\_backup\%date:~6,10%.%date:~3,2%.%date:~0,2%*.* C:\_backup\%date:~6,10%\%date:~3,2%\

REM Emitia um som ao término do backup... mas o gerente nao gostou da ideia
REM sounder aguia.wav

echo on