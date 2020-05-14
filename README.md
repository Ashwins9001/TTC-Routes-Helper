# TTC Route Finder
 Fetch list of stations and respective bus routes from TTC api, and when clicked, open Google Maps to show user location. REST calls made to server using RetroFit, and data populated using a custom array adapter. Different layouts used to separate loading screen from main activity. Intents used to make Google Maps search query. Custom API built to handle user creation, login, Presto balance update with local database to store information built across PHP and SQL.

- [x] Configured RetroFit and parsed JSON data for Finch station to Java objects
- [x] Configured ArrayAdapter<RouteList> to display combined boxes for stations and bus routes
- [x] Reduced resource usage with ViewHolder
- [x] Added loading screen
- [x] Added onClickListeners for elements
- [x] Set up Google Maps search query
- [x] Add API and local database with users to track Presto balance, can send requests for login, creation, balance change
- [] Integrate API and local db with app

<p float="left">
  <img src="https://github.com/Ashwins9001/TTC-Route-Finder/blob/master/Picture/Loading-Screen.png" width="420"/> 
  <img src="https://github.com/Ashwins9001/TTC-Route-Finder/blob/master/Picture/Main-Screen.png" width="420"/>

</p>

## Authors

* **Ashwin Singh**

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

Custom array adapter referenced: Hathibelagal A (https://github.com/hathibelagal)
