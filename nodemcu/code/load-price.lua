

PIN_CS  = 8 -- GPIO15, pull-down 10k to GND
PIN_DC  = 4 -- GPIO2
PIN_RES = 0 -- GPIO16
M_BUS   = 1

-- Initialise module
spi.setup(M_BUS, spi.MASTER, spi.CPOL_LOW, spi.CPHA_LOW, 8, 8)
--gpio.mode(cs, gpio.INPUT, gpio.PULLUP)
local disp = u8g2.pcd8544_84x48(M_BUS, PIN_CS, PIN_DC, PIN_RES)

disp:clearBuffer() -- start with clean buffer

--[[
Пришла строка типа 0.1.21.2.1.2.57
Смысл в чём. Предполагаем что первый симол 0. Выводим количество его.
Потом точка. Значит начинается следующее число. Это единицы, выводим их количество
Далее опять 0. И так по кругу
]]

function drawPixels (iterate, y, x_start, number)
    if iterate == 1 then
        disp:drawHLine(x_start, y, number)
        disp:sendBuffer() -- sent buffer to display
        --pwm.setup(6, 500, (y*20))
        --pwm.start(6)
    end

    iterate = nil
    y = nil
    x_start = nil
    number = nil
    collectgarbage()
end


function drawLine(numString, stringCode)
    local iterate = 0 --Что сейчас выводим
    local x_start = 0 --точка по иксу где начинаем рисование
    for l in string.gmatch(stringCode, "[^.]+") do
        drawPixels (iterate, numString, x_start, tonumber(l))

        if iterate == 1 then iterate = 0 else iterate = 1 end;
        x_start = x_start + tonumber(l)
        l = nil
        collectgarbage()
    end
    iterate = nil
    x_start = nil
    collectgarbage()

end;


if file.open("pice.img", "r") then
    for i = 0, 50 do
        if i == 0 then
            disp:setContrast(tonumber(file.readline()))
        elseif i == 1 then
            display_on = tonumber(file.readline())
        elseif i == 2 then
            display_pwm  = tonumber(file.readline())
        else
            drawLine((i-3), file.readline())
        end;
        file.flush()
    end
    file.close()
end

disp:sendBuffer() -- sent buffer to display
disp = nil
collectgarbage()

if display_on == 1 and display_pwm > 0 then
    pwm.setup(6, 500, display_pwm)
    pwm.start(6)
end