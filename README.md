Прошивка ноды  
==============

      esptool.py --port /dev/ttyUSB0 erase_flash
      esptool.py --port /dev/ttyUSB0 --baud 460800 write_flash --flash_size=detect 0 "nodemcu/firmware/file gpio http i2c mdns mqtt net node pwm sjson sntp spi tmr u8g2 uart wifi ws2812-2021-09-12-10-47-21-integer.bin"

