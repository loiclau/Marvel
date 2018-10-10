# Problem Statement

We are interested in triangles that have integer length sides, all of which are between
minLength and maxLength, inclusive. How many such triangles are there? Two triangles
differ if they have a different collection of side lengths, ignoring order. Triangles with
side lengths {2,3,4} and {4,3,5} differ, but {2,3,4} and {4,2,3} do not. We are only
interested in proper triangles; the sum of the two smallest sides of a proper triangle must
be strictly greater than the length of the biggest side.

Create a class TriCount that contains a method count that is given ints minLength and maxLength 
and returns the number of different proper triangles whose sides all have lengths between minLength 
and maxLength, inclusive. If there are more than 1,000,000,000 return -1.

## Definition
**Class**: TriCount
**Method**: count
**Parameters**: int, int
**Returns**: int
**Method signature**: int count(int minLength, int maxLength)
**Constraints** :
- minLength is between 1 and 1,000,000, inclusive. 
- maxLength is between minLength and 1,000,000, inclusive.

## Examples

1) 1->2 Returns 3 :
The proper triangles with side lengths between 1 and 2 inclusive are {1,1,1} and {2,2,2} and {1,2,2}.

2) 9->10 Returns 4 :
9,9,9 and 10,10,10 and 9,9,10 and 9,10,10

3) 1->1000000 Returns -1 :
There are VERY many triangles with lengths in this range.

4) 19->1000 Returns 83540657:
