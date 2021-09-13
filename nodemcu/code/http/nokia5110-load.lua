return function(connection, req, args)
   dofile("httpserver-header.lc")(connection, 200, 'html')
    connection:send("1");
    dofile("load-price.lc");
    collectgarbage()
end
