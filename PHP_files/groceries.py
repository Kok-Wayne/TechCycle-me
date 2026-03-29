groceries = ["apples", "tillamook", "toothpaste", "milk", "veggies"]
prices = [4.56, 21.20, 12.49, 7.39, 54.28]
program_status = True
counter = 0
student_discount = 0.9
basket = []
total = 0

print("======GROCERY ITEMS=======")
for grocery in groceries:
    print(grocery + " = " + str(prices[counter]))
    counter = counter + 1
    
while True:
    is_student = input("Are you a student? ")
    if is_student == "yes" or is_student == "Yes":
        is_student = True
        break
    elif is_student == "no" or is_student == "No":
        is_student = False
        break
    else:
        print("Yes or No only!")

while program_status:
    user_input = input("What do you want to buy? ")
    found = False
    grocery_finder = 0
    for grocery in groceries:
        if user_input == grocery: 
            total = total + prices[grocery_finder]
            basket.append(grocery)
            print(grocery + str(total))
            
        elif user_input != grocery:
            if user_input == "done":
                item_counter = 1
                print("======Reciept======")
                for item in basket:
                    print(str(item_counter) + ") " + item)
                    item_counter = item_counter + 1
                print("=======Total=======")
                if is_student:
                    total = total * student_discount
                    print("Applied discount: " + str((1 - student_discount) * 100))
                    print(total)
                else:
                    print(total)
                program_status = False
                found = True
                break
            
            else:
                for item in groceries:
                    if item == user_input:
                        found = True
                        break

                if found == False:
                    print("Not an applicable answer! Please choose from our list of foods or enter 'Done' to exit!")
                    break
        grocery_finder = grocery_finder + 1