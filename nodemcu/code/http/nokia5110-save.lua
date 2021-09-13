return function(connection, req, args)
   dofile("httpserver-header.lc")(connection, 200, 'html')

    if req.method == "GET" then
        connection:send("Send post")
    elseif req.method == "POST" then
        local rd = req.getRequestData()

        local contrast = 125;
        local blue = 0;
        local bluePwm = 0;
        local codeLine = "";
        for name, value in pairs(rd) do
            if name == "contrast" then
                contrast = value;
            end ;
            if name == "bluePwm" then
                bluePwm = value;
            end ;
            if name == "blue" then
                blue = value;
            end ;
            --Если код, то обрабатываем дисплей
            if name == "cl" then
                codeLine = value;
            end ;
        end ;

        file.open("pice.img", "w+")
        file.writeline(contrast) --контраст
        file.writeline(blue) --включён дисплей или нет
        file.writeline(bluePwm) -- яркость дисплея
        for k in string.gmatch(codeLine, "[^,]+") do
            file.writeline(k)
        end
        file.close()
        rd = nil
        contrast = nil
        blue = nil
        bluePwm = nil
        codeLine = nil
        collectgarbage()
    else
        connection:send("ERROR WTF req.method is ", req.method)
    end

    connection:send("1");

    collectgarbage()
end
